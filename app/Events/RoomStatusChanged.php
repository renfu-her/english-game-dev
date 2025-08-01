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

class RoomStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $room;
    public $oldStatus;
    public $newStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(Room $room, string $oldStatus, string $newStatus)
    {
        $this->room = $room;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
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
            new PresenceChannel('room.' . $this->room->id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        $statusMessages = [
            'waiting' => '等待中',
            'playing' => '遊戲中',
            'finished' => '已結束',
        ];

        return [
            'room_id' => $this->room->id,
            'room_name' => $this->room->name,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'old_status_text' => $statusMessages[$this->oldStatus] ?? $this->oldStatus,
            'new_status_text' => $statusMessages[$this->newStatus] ?? $this->newStatus,
            'message' => '房間 "' . $this->room->name . '" 狀態變更：' . 
                        ($statusMessages[$this->oldStatus] ?? $this->oldStatus) . ' → ' . 
                        ($statusMessages[$this->newStatus] ?? $this->newStatus),
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'room.status_changed';
    }
}
