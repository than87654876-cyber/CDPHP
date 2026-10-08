<?php

namespace App\Events;

use App\Models\Dish;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DishUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $action; // 'created', 'updated', 'deleted'
    public $dishId;
    public $dishData;
    public $renderedCard;

    /**
     * Create a new event instance.
     *
     * @param Dish|null $dish
     * @param string $action
     * @param int|null $dishId
     * @param string|null $renderedCard
     */
    public function __construct($dish, string $action = 'updated', ?int $dishId = null, ?string $renderedCard = null)
    {
        $this->action = $action;
        $this->dishId = $dishId ?? ($dish ? $dish->id : null);
        $this->renderedCard = $renderedCard;

        if ($dish) {
            $categoryName = $dish->category ? $dish->category->category_name : '';
            if (!$categoryName && $dish->category_id) {
                $cat = \App\Models\Category::find($dish->category_id);
                $categoryName = $cat ? $cat->category_name : '';
            }

            $this->dishData = [
                'id' => $dish->id,
                'dish_name' => $dish->dish_name,
                'category_id' => $dish->category_id,
                'category_name' => $categoryName,
                'price' => (float) $dish->price,
                'formatted_price' => number_format($dish->price) . 'đ',
                'description' => $dish->description,
                'display_image' => $dish->display_image,
                'is_available' => (bool) $dish->is_available,
                'rating_score' => $dish->rating_score,
                'reviews_count' => $dish->reviews_count,
            ];
        } else {
            $this->dishData = null;
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('dishes-channel'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'DishUpdated';
    }

    /**
     * Data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'action' => $this->action,
            'dish_id' => $this->dishId,
            'dish' => $this->dishData,
            'rendered_card' => $this->renderedCard,
            'timestamp' => microtime(true),
        ];
    }
}
