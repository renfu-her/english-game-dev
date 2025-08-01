<?php

namespace App\Events;

use App\Models\Room;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoomCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $room;

    /**
     * Create a new event instance.
     */
    public function __construct(Room $room)
    {
        $this->room = $room;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('game.lobby'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'room' => [
                'id' => $this->room->id,
                'name' => $this->room->name,
                'code' => $this->room->code,
                'status' => $this->room->status,
                'max_players' => $this->room->max_players,
                'current_players' => $this->room->players()->count(),
                'category' => [
                    'id' => $this->room->category->id,
                    'name' => $this->room->category->name,
                ],
                'difficulty' => $this->room->difficulty,
                'question_count' => $this->room->question_count,
                'time_limit' => $this->room->time_limit,
                'host' => [
                    'id' => $this->room->host->id,
                    'name' => $this->room->host->name,
                ],
                'created_at' => $this->room->created_at->toISOString(),
            ],
            'message' => '新房間 "' . $this->room->name . '" 已建立',
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'room.created';
    }
}
