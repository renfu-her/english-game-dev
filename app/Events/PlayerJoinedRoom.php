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

class PlayerJoinedRoom implements ShouldBroadcast
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
        // 重新計算玩家數量，確保包含新加入的玩家
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
            new Channel('room.' . $this->room->id),
            new Channel('game.lobby'),
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
            'message' => $this->player->name . ' 加入了房間',
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'player.joined';
    }
}
