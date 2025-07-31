<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class Member extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'total_games',
        'correct_answers',
        'total_answers',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'total_games' => 'integer',
            'correct_answers' => 'integer',
            'total_answers' => 'integer',
        ];
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'host_id');
    }

    public function roomPlayers(): HasMany
    {
        return $this->hasMany(RoomPlayer::class, 'member_id');
    }

    public function gameRecords(): HasMany
    {
        return $this->hasMany(GameRecord::class, 'member_id');
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'member_id');
    }

    public function getAccuracyAttribute(): float
    {
        if ($this->total_answers === 0) {
            return 0.0;
        }
        return round(($this->correct_answers / $this->total_answers) * 100, 1);
    }
}
