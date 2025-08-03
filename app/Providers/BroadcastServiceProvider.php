<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Broadcast::routes();

        // 手動註冊 Reverb 驅動
        Broadcast::extend('reverb', function ($app, $config) {
            return new \Illuminate\Broadcasting\Broadcasters\ReverbBroadcaster(
                $app['http'],
                $config['key'],
                $config['secret'],
                $config['app_id'],
                $config['options'] ?? []
            );
        });
    }
} 