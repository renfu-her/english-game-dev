<?php

namespace App\Events;

use App\Models\Room;
use App\Models\Question;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuestionDisplayed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $room;
    public $question;
    public $questionNumber;
    public $totalQuestions;
    public $timeLimit;

    /**
     * Create a new event instance.
     */
    public function __construct(Room $room, Question $question, int $questionNumber, int $totalQuestions)
    {
        $this->room = $room;
        $this->question = $question;
        $this->questionNumber = $questionNumber;
        $this->totalQuestions = $totalQuestions;
        $this->timeLimit = $room->time_limit;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('game.' . $this->room->id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'room_id' => $this->room->id,
            'question' => [
                'id' => $this->question->id,
                'question' => $this->question->question,
                'type' => $this->question->type,
                'options' => $this->question->options,
                'correct_answer' => $this->question->correct_answer,
                'explanation' => $this->question->explanation,
            ],
            'question_number' => $this->questionNumber,
            'total_questions' => $this->totalQuestions,
            'time_limit' => $this->timeLimit,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'question.displayed';
    }
}
