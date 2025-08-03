<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Broadcast;
use App\Events\TestEvent;

class TestReverbController extends Controller
{
    /**
     * 顯示 Reverb 測試頁面
     */
    public function index()
    {
        return view('test-reverb');
    }

    /**
     * 測試 Reverb 配置
     */
    public function testConfiguration()
    {
        $config = config('broadcasting');
        $reverbConfig = config('reverb');
        
        $result = [
            'broadcasting_default' => $config['default'] ?? 'unknown',
            'reverb_configured' => isset($config['connections']['reverb']),
            'reverb_config' => $config['connections']['reverb'] ?? null,
            'reverb_server_config' => $reverbConfig['servers']['reverb'] ?? null,
        ];
        
        return response()->json($result);
    }

    /**
     * 測試服務器連接
     */
    public function testServerConnection()
    {
        $host = config('broadcasting.connections.reverb.options.host', '127.0.0.1');
        $port = config('broadcasting.connections.reverb.options.port', 8080);
        $scheme = config('broadcasting.connections.reverb.options.scheme', 'http');
        
        $url = "{$scheme}://{$host}:{$port}";
        
        try {
            $response = Http::timeout(10)->get($url);
            
            return response()->json([
                'success' => true,
                'url' => $url,
                'status_code' => $response->status(),
                'response_time' => $response->handlerStats()['total_time'] ?? 0,
                'message' => $response->successful() ? '服務器連接成功' : '服務器響應異常'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'url' => $url,
                'error' => $e->getMessage(),
                'message' => '無法連接到服務器'
            ]);
        }
    }

    /**
     * 測試廣播功能
     */
    public function testBroadcasting(Request $request)
    {
        $message = $request->input('message', 'Web 介面測試訊息');
        
        try {
            $event = new TestEvent($message);
            Broadcast::dispatch($event);
            
            return response()->json([
                'success' => true,
                'message' => '事件廣播成功',
                'event_data' => [
                    'message' => $message,
                    'timestamp' => now()->toISOString()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => '廣播失敗',
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * 測試 WebSocket 端點
     */
    public function testWebSocketEndpoints()
    {
        $host = config('broadcasting.connections.reverb.options.host', '127.0.0.1');
        $port = config('broadcasting.connections.reverb.options.port', 8080);
        $scheme = config('broadcasting.connections.reverb.options.scheme', 'http');
        $appId = config('broadcasting.connections.reverb.app_id');
        
        $endpoints = [
            'events' => "{$scheme}://{$host}:{$port}/apps/{$appId}/events",
            'auth' => "{$scheme}://{$host}:{$port}/apps/{$appId}/auth",
        ];
        
        $results = [];
        
        foreach ($endpoints as $name => $endpoint) {
            try {
                $response = Http::timeout(5)->post($endpoint, [
                    'test' => 'data'
                ]);
                
                $results[$name] = [
                    'success' => true,
                    'url' => $endpoint,
                    'status_code' => $response->status(),
                    'reachable' => in_array($response->status(), [200, 404, 405])
                ];
            } catch (\Exception $e) {
                $results[$name] = [
                    'success' => false,
                    'url' => $endpoint,
                    'error' => $e->getMessage(),
                    'reachable' => false
                ];
            }
        }
        
        return response()->json($results);
    }

    /**
     * 測試頻道廣播
     */
    public function testChannelBroadcast(Request $request)
    {
        $channel = $request->input('channel', 'test-channel');
        $data = $request->input('data', ['message' => '測試訊息']);
        
        try {
            Broadcast::channel($channel, $data);
            
            return response()->json([
                'success' => true,
                'channel' => $channel,
                'data' => $data,
                'message' => '頻道廣播成功'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'channel' => $channel,
                'error' => $e->getMessage(),
                'message' => '頻道廣播失敗'
            ]);
        }
    }

    /**
     * 獲取環境變數狀態
     */
    public function getEnvironmentStatus()
    {
        $envVars = [
            'BROADCAST_CONNECTION',
            'REVERB_APP_KEY',
            'REVERB_APP_SECRET',
            'REVERB_APP_ID',
            'REVERB_HOST',
            'REVERB_PORT',
            'REVERB_SCHEME'
        ];
        
        $status = [];
        
        foreach ($envVars as $var) {
            $value = env($var);
            $status[$var] = [
                'set' => !empty($value),
                'value' => $value ? (strlen($value) > 20 ? substr($value, 0, 20) . '...' : $value) : null
            ];
        }
        
        return response()->json($status);
    }

    /**
     * 執行完整測試
     */
    public function runFullTest()
    {
        $results = [];
        
        // 1. 配置測試
        $config = config('broadcasting');
        $results['configuration'] = [
            'default_driver' => $config['default'] ?? 'unknown',
            'reverb_configured' => isset($config['connections']['reverb']),
            'success' => ($config['default'] === 'reverb' && isset($config['connections']['reverb']))
        ];
        
        // 2. 服務器連接測試
        $host = config('broadcasting.connections.reverb.options.host', '127.0.0.1');
        $port = config('broadcasting.connections.reverb.options.port', 8080);
        $scheme = config('broadcasting.connections.reverb.options.scheme', 'http');
        $url = "{$scheme}://{$host}:{$port}";
        
        try {
            $response = Http::timeout(5)->get($url);
            $results['server_connection'] = [
                'success' => ($response->successful() || $response->status() === 404),
                'status_code' => $response->status(),
                'url' => $url
            ];
        } catch (\Exception $e) {
            $results['server_connection'] = [
                'success' => false,
                'error' => $e->getMessage(),
                'url' => $url
            ];
        }
        
        // 3. 廣播測試
        try {
            $event = new TestEvent('完整測試訊息');
            Broadcast::dispatch($event);
            $results['broadcasting'] = [
                'success' => true,
                'message' => '廣播功能正常'
            ];
        } catch (\Exception $e) {
            $results['broadcasting'] = [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
        
        return response()->json($results);
    }
} 