<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Broadcast;
use App\Events\TestEvent;

class TestReverbConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reverb:test {--verbose : 顯示詳細信息}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '測試 Laravel Reverb 連接和廣播功能';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔌 開始測試 Laravel Reverb 連接...');
        
        $verbose = $this->option('verbose');
        
        // 1. 測試配置
        $this->testConfiguration($verbose);
        
        // 2. 測試服務器連接
        $this->testServerConnection($verbose);
        
        // 3. 測試廣播功能
        $this->testBroadcasting($verbose);
        
        // 4. 測試 WebSocket 端點
        $this->testWebSocketEndpoints($verbose);
        
        $this->info('✅ Reverb 測試完成！');
    }

    /**
     * 測試配置
     */
    private function testConfiguration($verbose)
    {
        $this->info('📋 測試配置...');
        
        $broadcastingConfig = config('broadcasting');
        $reverbConfig = config('reverb');
        
        if ($verbose) {
            $this->line('Broadcasting 配置:');
            $this->line('- 默認驅動: ' . $broadcastingConfig['default']);
            $this->line('- Reverb 配置存在: ' . (isset($broadcastingConfig['connections']['reverb']) ? '是' : '否'));
        }
        
        if ($broadcastingConfig['default'] !== 'reverb') {
            $this->warn('⚠️  警告: 默認廣播驅動不是 reverb');
        }
        
        if (!isset($broadcastingConfig['connections']['reverb'])) {
            $this->error('❌ 錯誤: 找不到 Reverb 配置');
            return false;
        }
        
        $this->info('✅ 配置測試通過');
        return true;
    }

    /**
     * 測試服務器連接
     */
    private function testServerConnection($verbose)
    {
        $this->info('🌐 測試服務器連接...');
        
        $host = config('broadcasting.connections.reverb.options.host', '127.0.0.1');
        $port = config('broadcasting.connections.reverb.options.port', 8080);
        $scheme = config('broadcasting.connections.reverb.options.scheme', 'http');
        
        $url = "{$scheme}://{$host}:{$port}";
        
        if ($verbose) {
            $this->line("嘗試連接到: {$url}");
        }
        
        try {
            $response = Http::timeout(10)->get($url);
            
            if ($verbose) {
                $this->line("響應狀態: " . $response->status());
                $this->line("響應內容: " . substr($response->body(), 0, 100) . '...');
            }
            
            if ($response->successful() || $response->status() === 404) {
                $this->info('✅ 服務器連接成功');
                return true;
            } else {
                $this->warn("⚠️  服務器響應異常: " . $response->status());
                return false;
            }
        } catch (\Exception $e) {
            $this->error("❌ 無法連接到服務器: " . $e->getMessage());
            return false;
        }
    }

    /**
     * 測試廣播功能
     */
    private function testBroadcasting($verbose)
    {
        $this->info('📡 測試廣播功能...');
        
        try {
            $event = new TestEvent('命令行測試訊息');
            Broadcast::dispatch($event);
            
            if ($verbose) {
                $this->line('事件已成功廣播');
            }
            
            $this->info('✅ 廣播功能正常');
            return true;
        } catch (\Exception $e) {
            $this->error("❌ 廣播失敗: " . $e->getMessage());
            return false;
        }
    }

    /**
     * 測試 WebSocket 端點
     */
    private function testWebSocketEndpoints($verbose)
    {
        $this->info('🔌 測試 WebSocket 端點...');
        
        $host = config('broadcasting.connections.reverb.options.host', '127.0.0.1');
        $port = config('broadcasting.connections.reverb.options.port', 8080);
        $scheme = config('broadcasting.connections.reverb.options.scheme', 'http');
        $appId = config('broadcasting.connections.reverb.app_id');
        
        $endpoints = [
            "{$scheme}://{$host}:{$port}/apps/{$appId}/events",
            "{$scheme}://{$host}:{$port}/apps/{$appId}/auth",
        ];
        
        foreach ($endpoints as $endpoint) {
            if ($verbose) {
                $this->line("測試端點: {$endpoint}");
            }
            
            try {
                $response = Http::timeout(5)->post($endpoint, [
                    'test' => 'data'
                ]);
                
                if ($verbose) {
                    $this->line("響應狀態: " . $response->status());
                }
                
                if (in_array($response->status(), [200, 404, 405])) {
                    $this->info("✅ 端點 {$endpoint} 可達");
                } else {
                    $this->warn("⚠️  端點 {$endpoint} 響應異常: " . $response->status());
                }
            } catch (\Exception $e) {
                $this->error("❌ 端點 {$endpoint} 無法連接: " . $e->getMessage());
            }
        }
    }
} 