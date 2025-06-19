<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTopicQuiz extends Model
{
    /** @use HasFactory<\Database\Factories\UserTopicQuizFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'lesson_id',
        'topic_id',
        'correct_count',
        'total_count',
        'score',
        'passed',
        'attempt_number',
    ];

    /**
     * Define a relação com o usuário.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define a relação com o tópico.
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Define a relação com a aula.
     */
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
