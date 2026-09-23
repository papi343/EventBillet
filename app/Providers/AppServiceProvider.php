<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Cache\RateLimiting\Limit;
use App\Http\Requests\Api\V1\UserLogin;
use App\Http\Requests\Api\V1\UserRegister;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('login', function(UserLogin $request){
            $key = Str::transliterate(
                Str::Lower($request->input('email'))."|".$request->ip()
            );
            return Limit::perMinute(5)->by($key);
        });

        RateLimiter::for('register',function(UserRegister $request){
            return Limit::perMinute(2)->by($request->ip());
        });
    }
}
