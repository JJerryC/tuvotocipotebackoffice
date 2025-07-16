<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class RateLimitServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        RateLimiter::for('candidates-api', function (Request $request) {
           
            $key = $request->header('X-API-KEY') ?: $request->ip();
            return Limit::perMinute(10000)->by($key);
        });
    }
}
