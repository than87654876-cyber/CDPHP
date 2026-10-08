<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PushNotificationSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $title;
    public $body;
    public $icon;
    public $url;
    public $type;
    public $timestamp;

    /**
     * Create a new event instance.
     *
     * @param string $title
     * @param string $body
     * @param string|null $url
     * @param string|null $icon
     * @param string $type
     */
    public function __construct(string $title, string $body, ?string $url = '/', ?string $icon = null, string $type = 'lunch')
    {
        $this->title = $title;
        $this->body = $body;
        $this->url = $url ?: '/';
        $this->icon = $icon ?: '/icons/icon-192x192.png';
        $this->type = $type;
        $this->timestamp = now()->toISOString();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('notifications-channel'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'PushNotificationSent';
    }

    /**
     * Data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'icon' => $this->icon,
            'url' => $this->url,
            'type' => $this->type,
            'timestamp' => $this->timestamp,
        ];
    }
}
