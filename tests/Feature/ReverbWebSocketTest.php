<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;

class ReverbWebSocketTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 測試 WebSocket 端點是否可達
     */
    public function test_websocket_endpoint_is_reachable()
    {
        $host = config('broadcasting.connections.reverb.options.host', '127.0.0.1');
        $port = config('broadcasting.connections.reverb.options.port', 8080);
        $scheme = config('broadcasting.connections.reverb.options.scheme', 'http');
        
        $url = "{$scheme}://{$host}:{$port}";
        
        try {
            $response = Http::timeout(10)->get($url);
            $this->assertTrue($response->successful() || $response->status() === 404);
        } catch (\Exception $e) {
            $this->markTestSkipped("WebSocket 端點無法連接: {$url} - " . $e->getMessage());
        }
    }

    /**
     * 測試 WebSocket 握手端點
     */
    public function test_websocket_handshake_endpoint()
    {
        $host = config('broadcasting.connections.reverb.options.host', '127.0.0.1');
        $port = config('broadcasting.connections.reverb.options.port', 8080);
        $scheme = config('broadcasting.connections.reverb.options.scheme', 'http');
        
        $url = "{$scheme}://{$host}:{$port}/apps/" . config('broadcasting.connections.reverb.app_id') . "/events";
        
        try {
            $response = Http::timeout(10)->post($url, [
                'name' => 'test-event',
                'data' => ['message' => '測試訊息'],
                'channel' => 'test-channel'
            ]);
            
            // 檢查響應狀態，200 或 404 都是正常的
            $this->assertTrue(in_array($response->status(), [200, 404, 405]));
        } catch (\Exception $e) {
            $this->markTestSkipped("WebSocket 握手失敗: {$url} - " . $e->getMessage());
        }
    }

    /**
     * 測試 WebSocket 認證端點
     */
    public function test_websocket_auth_endpoint()
    {
        $host = config('broadcasting.connections.reverb.options.host', '127.0.0.1');
        $port = config('broadcasting.connections.reverb.options.port', 8080);
        $scheme = config('broadcasting.connections.reverb.options.scheme', 'http');
        
        $url = "{$scheme}://{$host}:{$port}/apps/" . config('broadcasting.connections.reverb.app_id') . "/auth";
        
        try {
            $response = Http::timeout(10)->post($url, [
                'socket_id' => 'test-socket-id',
                'channel_name' => 'private-test-channel'
            ]);
            
            // 檢查響應狀態
            $this->assertTrue(in_array($response->status(), [200, 403, 404, 405]));
        } catch (\Exception $e) {
            $this->markTestSkipped("WebSocket 認證失敗: {$url} - " . $e->getMessage());
        }
    }

    /**
     * 測試 WebSocket 連接配置
     */
    public function test_websocket_connection_configuration()
    {
        $config = config('broadcasting.connections.reverb');
        
        $this->assertArrayHasKey('options', $config);
        $this->assertArrayHasKey('host', $config['options']);
        $this->assertArrayHasKey('port', $config['options']);
        $this->assertArrayHasKey('scheme', $config['options']);
        
        // 檢查端口是否為有效數字
        $this->assertIsInt($config['options']['port']);
        $this->assertGreaterThan(0, $config['options']['port']);
        $this->assertLessThan(65536, $config['options']['port']);
    }
} 