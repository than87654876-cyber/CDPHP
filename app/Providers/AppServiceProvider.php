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

        // 2. Share sidebar badge counts for admin layout (cached 15s with single SQL query)
        \Illuminate\Support\Facades\View::composer('layouts.admin', function ($view) {
            $badges = \Illuminate\Support\Facades\Cache::remember('admin_sidebar_counts', 15, function () {
                try {
                    $orderCounts = \Illuminate\Support\Facades\DB::table('orders')
                        ->selectRaw("
                            COUNT(CASE WHEN order_status = 'preparing' THEN 1 END) as pending,
                            COUNT(CASE WHEN order_status = 'delivering' THEN 1 END) as delivering,
                            COUNT(CASE WHEN order_status IN ('confirmed', 'preparing') THEN 1 END) as kitchen
                        ")->first();

                    $lowStock = 0;
                    if (\Illuminate\Support\Facades\Schema::hasTable('inventory_items')) {
                        $lowStock = (int) \Illuminate\Support\Facades\DB::table('inventory_items')
                            ->whereRaw('current_stock <= min_stock')
                            ->count();
                    }

                    return [
                        'pendingOrdersCount' => (int) ($orderCounts->pending ?? 0),
                        'deliveringOrdersCount' => (int) ($orderCounts->delivering ?? 0),
                        'kitchenCount' => (int) ($orderCounts->kitchen ?? 0),
                        'lowStockInventoryCount' => $lowStock,
                    ];
                } catch (\Throwable $e) {
                    return [
                        'pendingOrdersCount' => 0,
                        'deliveringOrdersCount' => 0,
                        'kitchenCount' => 0,
                        'lowStockInventoryCount' => 0,
                    ];
                }
            });

            $view->with($badges);
        });
    }
}
