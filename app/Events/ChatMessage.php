<?php

namespace App\Events;

use App\Models\Room;
use App\Models\Member;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $room;
    public $sender;
    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Room $room, Member $sender, string $message)
    {
        $this->room = $room;
        $this->sender = $sender;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('room.' . $this->room->id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'room_id' => $this->room->id,
            'sender' => [
                'id' => $this->sender->id,
                'name' => $this->sender->name,
                'avatar' => $this->sender->avatar,
            ],
            'message' => $this->message,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'chat.message';
    }
}
