<?php

namespace App\Services;

use App\Events\DishUpdated;
use App\Models\Dish;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DishSyncService
{
    /**
     * Record a dish mutation (create, update, delete) and trigger real-time sync.
     *
     * @param string $action 'created'|'updated'|'deleted'
     * @param Dish|null $dish
     * @param int|null $dishId
     * @return array
     */
    public static function recordMutation(string $action, ?Dish $dish, ?int $dishId = null): array
    {
        $resolvedDishId = $dishId ?? ($dish ? $dish->id : null);
        $currentTime = microtime(true);

        // 1. Update mutation timestamp in cache
        Cache::forever('dishes_last_mutation_timestamp', $currentTime);

        // 2. Clear dish-related caches
        Cache::forget('gemini_dishes_menu');
        Cache::forget('shop_top_dish_id');
        Cache::forget('shop_top_dish_ids');
        Dish::clearReviewsSummaryCache();

        // 3. Render card partial for seamless instant DOM injection
        $renderedCard = null;
        if ($dish && $dish->is_available) {
            try {
                $renderedCard = view('client.partials.dish_card', [
                    'dish' => $dish,
                    'dIndex' => 0,
                    'isNew' => ($action === 'created'),
                ])->render();
            } catch (\Throwable $e) {
                Log::warning('DishSyncService renderedCard failed: ' . $e->getMessage());
            }
        }

        // 4. Build mutation payload
        $mutation = [
            'mutation_id' => (string) Str::uuid(),
            'action' => $action,
            'dish_id' => $resolvedDishId,
            'category_id' => $dish ? $dish->category_id : null,
            'timestamp' => $currentTime,
            'dish' => $dish ? [
                'id' => $dish->id,
                'dish_name' => $dish->dish_name,
                'category_id' => $dish->category_id,
                'category_name' => $dish->category ? $dish->category->category_name : '',
                'price' => (float) $dish->price,
                'formatted_price' => number_format($dish->price) . 'đ',
                'description' => $dish->description,
                'display_image' => $dish->display_image,
                'is_available' => (bool) $dish->is_available,
                'rating_score' => $dish->rating_score,
                'reviews_count' => $dish->reviews_count,
            ] : null,
            'rendered_card' => $renderedCard,
        ];

        // 5. Store in rolling recent mutations list
        $recent = Cache::get('dishes_recent_mutations', []);
        if (!is_array($recent)) {
            $recent = [];
        }
        $recent[] = $mutation;
        if (count($recent) > 30) {
            $recent = array_slice($recent, -30);
        }
        Cache::put('dishes_recent_mutations', $recent, 3600);

        // 6. Broadcast event via WebSocket (Reverb / Pusher)
        try {
            $shouldBroadcast = true;
            $defaultDriver = config('broadcasting.default');

            // If using local Reverb, quickly check if port is listening (timeout 0.1s)
            // to avoid 30s connection timeout freezes when reverb server is inactive
            if ($defaultDriver === 'reverb') {
                $host = config('broadcasting.connections.reverb.options.host', '127.0.0.1');
                $port = (int) config('broadcasting.connections.reverb.options.port', 8080);
                $socket = @fsockopen($host, $port, $errno, $errstr, 0.1);
                if ($socket) {
                    fclose($socket);
                } else {
                    $shouldBroadcast = false;
                }
            }

            if ($shouldBroadcast) {
                broadcast(new DishUpdated($dish, $action, $resolvedDishId, $renderedCard));
            }
        } catch (\Throwable $e) {
            Log::warning('DishSyncService broadcast error: ' . $e->getMessage());
        }

        return $mutation;
    }

    /**
     * Get mutations that occurred since a given microtime timestamp.
     *
     * @param float $sinceTime
     * @return array
     */
    public static function getMutationsSince(float $sinceTime): array
    {
        $lastMutationTime = self::getLastMutationTimestamp();
        if ($lastMutationTime <= $sinceTime) {
            return [];
        }

        $recent = Cache::get('dishes_recent_mutations', []);
        if (!is_array($recent) || empty($recent)) {
            return [];
        }

        return array_values(array_filter($recent, function ($m) use ($sinceTime) {
            return isset($m['timestamp']) && (float) $m['timestamp'] > $sinceTime;
        }));
    }

    /**
     * Get the latest mutation microtime timestamp.
     *
     * @return float
     */
    public static function getLastMutationTimestamp(): float
    {
        return (float) Cache::get('dishes_last_mutation_timestamp', 0);
    }
}
