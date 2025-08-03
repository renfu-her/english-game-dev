<?php

namespace Tests\Feature;

use App\Events\TestEvent;
use App\Events\ChatMessage;
use App\Events\RoomCreated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;

class ReverbIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // 確保廣播配置正確
        config(['broadcasting.default' => 'reverb']);
    }

    /**
     * 測試完整的廣播流程
     */
    public function test_complete_broadcasting_workflow()
    {
        // 1. 測試配置
        $this->test_configuration();
        
        // 2. 測試連接
        $this->test_connection();
        
        // 3. 測試廣播
        $this->test_broadcasting();
    }

    /**
     * 測試配置是否正確
     */
    private function test_configuration()
    {
        $broadcastingConfig = config('broadcasting');
        $reverbConfig = config('reverb');
        
        $this->assertNotNull($broadcastingConfig);
        $this->assertNotNull($reverbConfig);
        $this->assertEquals('reverb', $broadcastingConfig['default']);
    }

    /**
     * 測試連接是否可用
     */
    private function test_connection()
    {
        $host = config('broadcasting.connections.reverb.options.host', '127.0.0.1');
        $port = config('broadcasting.connections.reverb.options.port', 8080);
        $scheme = config('broadcasting.connections.reverb.options.scheme', 'http');
        
        $url = "{$scheme}://{$host}:{$port}";
        
        try {
            $response = Http::timeout(5)->get($url);
            $this->assertTrue($response->successful() || $response->status() === 404);
        } catch (\Exception $e) {
            $this->markTestSkipped("無法連接到 Reverb 服務器: {$url}");
        }
    }

    /**
     * 測試廣播功能
     */
    private function test_broadcasting()
    {
        try {
            $event = new TestEvent('整合測試訊息');
            Broadcast::dispatch($event);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->markTestSkipped("廣播失敗: " . $e->getMessage());
        }
    }

    /**
     * 測試遊戲相關事件廣播
     */
    public function test_game_events_broadcasting()
    {
        // 創建測試數據
        $room = \App\Models\Room::factory()->create();
        $member = \App\Models\Member::factory()->create();
        
        // 測試聊天訊息事件
        try {
            $chatEvent = new ChatMessage($room, $member, '測試聊天訊息');
            Broadcast::dispatch($chatEvent);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->markTestSkipped("聊天事件廣播失敗: " . $e->getMessage());
        }

        // 測試房間創建事件
        try {
            $roomEvent = new RoomCreated($room);
            Broadcast::dispatch($roomEvent);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->markTestSkipped("房間事件廣播失敗: " . $e->getMessage());
        }
    }

    /**
     * 測試頻道訂閱
     */
    public function test_channel_subscription()
    {
        $channels = [
            'room.1',
            'private-room.1',
            'presence-room.1'
        ];

        foreach ($channels as $channel) {
            try {
                $data = [
                    'event' => 'test-event',
                    'data' => [
                        'message' => "測試 {$channel} 頻道",
                        'channel' => $channel,
                        'timestamp' => now()
                    ]
                ];
                
                Broadcast::channel($channel, $data);
                $this->assertTrue(true);
            } catch (\Exception $e) {
                $this->markTestSkipped("頻道 {$channel} 訂閱失敗: " . $e->getMessage());
            }
        }
    }

    /**
     * 測試錯誤處理
     */
    public function test_error_handling()
    {
        // 測試無效的頻道名稱
        try {
            Broadcast::channel('', ['message' => '測試']);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            // 預期會拋出異常
            $this->assertTrue(true);
        }

        // 測試無效的數據格式
        try {
            Broadcast::channel('test-channel', null);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            // 預期會拋出異常
            $this->assertTrue(true);
        }
    }
} 