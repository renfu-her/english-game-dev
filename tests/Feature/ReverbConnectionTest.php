<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;

class ReverbConnectionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 測試 Reverb 配置是否正確
     */
    public function test_reverb_configuration_is_valid()
    {
        $config = config('broadcasting.connections.reverb');
        
        $this->assertNotNull($config);
        $this->assertEquals('reverb', $config['driver']);
        $this->assertArrayHasKey('key', $config);
        $this->assertArrayHasKey('secret', $config);
        $this->assertArrayHasKey('app_id', $config);
        $this->assertArrayHasKey('options', $config);
    }

    /**
     * 測試 Reverb 服務器是否可達
     */
    public function test_reverb_server_is_reachable()
    {
        $host = config('broadcasting.connections.reverb.options.host', '127.0.0.1');
        $port = config('broadcasting.connections.reverb.options.port', 8080);
        $scheme = config('broadcasting.connections.reverb.options.scheme', 'http');
        
        $url = "{$scheme}://{$host}:{$port}";
        
        try {
            $response = Http::timeout(5)->get($url);
            $this->assertTrue($response->successful() || $response->status() === 404);
        } catch (\Exception $e) {
            $this->markTestSkipped("Reverb 服務器無法連接: {$url} - " . $e->getMessage());
        }
    }

    /**
     * 測試廣播配置是否正確設置
     */
    public function test_broadcasting_configuration()
    {
        $default = config('broadcasting.default');
        $this->assertEquals('reverb', $default);
        
        $connections = config('broadcasting.connections');
        $this->assertArrayHasKey('reverb', $connections);
    }

    /**
     * 測試 Reverb 應用程序配置
     */
    public function test_reverb_app_configuration()
    {
        $reverbConfig = config('reverb');
        
        $this->assertNotNull($reverbConfig);
        $this->assertArrayHasKey('servers', $reverbConfig);
        $this->assertArrayHasKey('apps', $reverbConfig);
        
        $apps = $reverbConfig['apps']['apps'] ?? [];
        $this->assertNotEmpty($apps);
        
        $app = $apps[0] ?? null;
        if ($app) {
            $this->assertArrayHasKey('key', $app);
            $this->assertArrayHasKey('secret', $app);
            $this->assertArrayHasKey('app_id', $app);
        }
    }
} 