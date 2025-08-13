<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            $hour = Carbon::now('Asia/Jakarta')->format('H');
            if ($hour >= 5 && $hour < 12) {
                $greeting = 'Selamat pagi!';
            } elseif ($hour >= 12 && $hour < 15) {
                $greeting = 'Selamat siang!';
            } elseif ($hour >= 15 && $hour < 18) {
                $greeting = 'Selamat sore!';
            } else {
                $greeting = 'Selamat malam!';
            }

            $view->with('greeting', $greeting);
        });
    }
}
