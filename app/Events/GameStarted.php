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

class GameStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $room;
    public $players;

    /**
     * Create a new event instance.
     */
    public function __construct(Room $room)
    {
        $this->room = $room;
        $this->players = $room->players()->with('member')->get();
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
            'room_name' => $this->room->name,
            'status' => 'playing',
            'players' => $this->players->map(function ($player) {
                return [
                    'id' => $player->member->id,
                    'name' => $player->member->name,
                    'avatar' => $player->member->avatar,
                    'is_ready' => $player->is_ready,
                    'is_host' => $player->member_id === $this->room->host_id,
                ];
            }),
            'game_settings' => [
                'category' => $this->room->category->name ?? '未分類',
                'difficulty' => $this->room->difficulty,
                'question_count' => $this->room->question_count,
                'time_limit' => $this->room->time_limit,
                'allow_skip' => $this->room->allow_skip,
                'show_explanation' => $this->room->show_explanation,
            ],
            'message' => '遊戲開始！',
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'game.started';
    }
}
