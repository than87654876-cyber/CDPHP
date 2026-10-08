<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $action;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, $action = 'updated')
    {
        $this->order = $order->load(['user', 'orderItems.dish']);
        $this->action = $action;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return [
            new Channel('kitchen-channel'),
            new PrivateChannel('App.Models.User.' . $this->order->user_id),
        ];
    }

    /**
     * Determine if this event should broadcast.
     */
    public function broadcastWhen(): bool
    {
        if (config('broadcasting.default') === 'reverb') {
            $host = config('broadcasting.connections.reverb.options.host', '127.0.0.1');
            $port = (int) config('broadcasting.connections.reverb.options.port', 8080);
            $socket = @fsockopen($host, $port, $errno, $errstr, 0.05);
            if ($socket) {
                fclose($socket);
                return true;
            }
            return false;
        }
        return true;
    }
}
