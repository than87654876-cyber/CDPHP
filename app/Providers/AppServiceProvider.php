<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // 1. Share settings for client layouts (cached for high performance)
        \Illuminate\Support\Facades\View::composer(['layouts.app', 'client.*'], function ($view) {
            $globalSettings = \Illuminate\Support\Facades\Cache::remember('app_global_settings', 300, function () {
                try {
                    if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                        return \App\Models\Setting::pluck('value', 'key')->all();
                    }
                } catch (\Throwable $e) {}
                return [];
            });

            $siteBgUrl = isset($globalSettings['site_background']) && $globalSettings['site_background'] 
                ? (\Illuminate\Support\Str::startsWith($globalSettings['site_background'], 'http') ? $globalSettings['site_background'] : asset($globalSettings['site_background']))
                : asset('uploads/cf1a02d49dc2b801e809fcb9adefd77e.jpg');

            $siteLogoUrl = isset($globalSettings['logo_url']) && $globalSettings['logo_url']
                ? (\Illuminate\Support\Str::startsWith($globalSettings['logo_url'], 'http') ? $globalSettings['logo_url'] : asset($globalSettings['logo_url']))
                : asset('logo.jpg');

            $view->with([
                'globalSettings' => $globalSettings,
                'siteBgUrl' => $siteBgUrl,
                'siteLogoUrl' => $siteLogoUrl,
            ]);
        });

        // 2. Share sidebar badge counts for admin layout (cached 10s to avoid per-request queries)
        \Illuminate\Support\Facades\View::composer('layouts.admin', function ($view) {
            $badges = \Illuminate\Support\Facades\Cache::remember('admin_sidebar_badges', 10, function () {
                try {
                    return [
                        'pendingOrdersCount' => \App\Models\Order::where('order_status', 'preparing')->count(),
                        'deliveringOrdersCount' => \App\Models\Order::where('order_status', 'delivering')->count(),
                        'kitchenCount' => \App\Models\Order::whereIn('order_status', ['confirmed', 'preparing'])->count(),
                        'refundCount' => \App\Models\Order::where('health_notes', 'like', '%[Yêu cầu hoàn tiền%')->where('payment_status', '!=', 'refunded')->count(),
                    ];
                } catch (\Throwable $e) {
                    return [
                        'pendingOrdersCount' => 0,
                        'deliveringOrdersCount' => 0,
                        'kitchenCount' => 0,
                        'refundCount' => 0,
                    ];
                }
            });

            $view->with($badges);
        });
    }
}
