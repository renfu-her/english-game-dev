<?php

namespace App\Events;

use App\Models\Member;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MemberStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $member;
    public $status;
    public $roomId;

    /**
     * Create a new event instance.
     */
    public function __construct(Member $member, string $status, ?int $roomId = null)
    {
        $this->member = $member;
        $this->status = $status;
        $this->roomId = $roomId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [
            new Channel('game.lobby'),
        ];

        if ($this->roomId) {
            $channels[] = new Channel('room.' . $this->roomId);
        }

        return $channels;
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        $statusMessages = [
            'online' => '上線',
            'offline' => '離線',
            'in_room' => '在房間中',
            'ready' => '已準備',
            'not_ready' => '未準備',
            'playing' => '遊戲中',
            'left_room' => '離開房間',
        ];

        return [
            'member' => [
                'id' => $this->member->id,
                'name' => $this->member->name,
                'avatar' => $this->member->avatar,
            ],
            'status' => $this->status,
            'status_text' => $statusMessages[$this->status] ?? $this->status,
            'room_id' => $this->roomId,
            'timestamp' => now()->toISOString(),
            'message' => $this->member->name . ' ' . ($statusMessages[$this->status] ?? $this->status),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'member.status_changed';
    }
}
