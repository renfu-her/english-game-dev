<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomPlayer extends Model
{
    protected $fillable = [
        'room_id',
        'user_id',
        'is_ready',
        'score',
        'joined_at',
        'left_at',
    ];

    protected $casts = [
        'is_ready' => 'boolean',
        'score' => 'integer',
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'user_id');
    }
}
