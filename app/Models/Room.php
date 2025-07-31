<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'host_id',
        'max_players',
        'current_players',
        'status',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
        'current_players' => 'integer',
        'max_players' => 'integer',
    ];

    public function host(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'host_id');
    }

    public function players(): HasMany
    {
        return $this->hasMany(RoomPlayer::class);
    }

    public function gameRecords(): HasMany
    {
        return $this->hasMany(GameRecord::class);
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }
}
