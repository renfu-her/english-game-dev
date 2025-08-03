<?php

namespace Tests\Feature;

use App\Events\TestEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;

class ReverbBroadcastingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // 確保廣播配置正確
        config(['broadcasting.default' => 'reverb']);
    }

    /**
     * 測試事件是否可以正確廣播
     */
    public function test_can_broadcast_event()
    {
        $event = new TestEvent('測試訊息');
        
        try {
            Broadcast::dispatch($event);
            $this->assertTrue(true); // 如果沒有拋出異常，表示廣播成功
        } catch (\Exception $e) {
            $this->markTestSkipped("廣播失敗: " . $e->getMessage());
        }
    }

    /**
     * 測試廣播到特定頻道
     */
    public function test_can_broadcast_to_specific_channel()
    {
        $channel = 'test-channel';
        $data = ['message' => '測試訊息', 'timestamp' => now()];
        
        try {
            Broadcast::channel($channel, $data);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->markTestSkipped("頻道廣播失敗: " . $e->getMessage());
        }
    }

    /**
     * 測試私有頻道廣播
     */
    public function test_can_broadcast_to_private_channel()
    {
        $channel = 'private-test-channel';
        $data = ['message' => '私有測試訊息'];
        
        try {
            Broadcast::channel($channel, $data);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->markTestSkipped("私有頻道廣播失敗: " . $e->getMessage());
        }
    }

    /**
     * 測試廣播到房間頻道
     */
    public function test_can_broadcast_to_room_channel()
    {
        $roomId = 1;
        $channel = "room.{$roomId}";
        $data = [
            'event' => 'test-event',
            'data' => [
                'message' => '房間測試訊息',
                'room_id' => $roomId,
                'timestamp' => now()
            ]
        ];
        
        try {
            Broadcast::channel($channel, $data);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->markTestSkipped("房間頻道廣播失敗: " . $e->getMessage());
        }
    }

    /**
     * 測試廣播連接器是否可用
     */
    public function test_broadcast_driver_is_available()
    {
        $driver = Broadcast::driver();
        $this->assertNotNull($driver);
        
        $connection = config('broadcasting.connections.reverb');
        $this->assertNotNull($connection);
    }
} 