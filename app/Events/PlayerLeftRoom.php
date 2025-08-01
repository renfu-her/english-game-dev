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

class PlayerLeftRoom implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $room;
    public $player;
    public $playerCount;

    /**
     * Create a new event instance.
     */
    public function __construct(Room $room, Member $player)
    {
        $this->room = $room;
        $this->player = $player;
        $this->playerCount = $room->players()->count();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('room.' . $this->room->id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'room_id' => $this->room->id,
            'player' => [
                'id' => $this->player->id,
                'name' => $this->player->name,
                'avatar' => $this->player->avatar,
            ],
            'player_count' => $this->playerCount,
            'max_players' => $this->room->max_players,
            'message' => $this->player->name . ' 離開了房間',
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'player.left';
    }
}
