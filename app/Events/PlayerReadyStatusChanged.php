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

class PlayerReadyStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $room;
    public $player;
    public $isReady;

    /**
     * Create a new event instance.
     */
    public function __construct(Room $room, Member $player, bool $isReady)
    {
        $this->room = $room;
        $this->player = $player;
        $this->isReady = $isReady;
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
            'is_ready' => $this->isReady,
            'message' => $this->player->name . ' ' . ($this->isReady ? '已準備' : '取消準備'),
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'player.ready_status_changed';
    }
}
