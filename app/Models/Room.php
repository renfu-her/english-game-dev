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
        'category_id',
        'max_players',
        'question_count',
        'time_limit',
        'is_private',
        'password',
        'status',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
        'max_players' => 'integer',
        'question_count' => 'integer',
        'time_limit' => 'integer',
        'is_private' => 'boolean',
    ];

    public function host(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'host_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
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
