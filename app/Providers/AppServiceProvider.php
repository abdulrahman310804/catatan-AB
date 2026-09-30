<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $userId = DB::table('users')->where('email', 'abdul@demo.com')->value('id');
            $user = $userId ? DB::table('users')->where('id', $userId)->first() : null;
            $reminderCount = $userId
                ? DB::table('reminders')->where('user_id', $userId)->where('is_sent', false)->count()
                : 0;

            $view->with([
                'layoutUser' => $user,
                'layoutReminderCount' => $reminderCount,
            ]);
        });
    }
}