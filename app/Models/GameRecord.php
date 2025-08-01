<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'member_id',
        'question_id',
        'answer',
        'is_correct',
        'time_taken',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'time_taken' => 'integer',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
