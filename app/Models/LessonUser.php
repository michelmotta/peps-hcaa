<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class LessonUser extends Pivot
{
    /** @use HasFactory<\Database\Factories\LessonUserFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'lesson_id',
        'finished',
        'created_at',
        'finished_at',
        'score',
        'quiz_locked_topic_id',
    ];

    /**
     * The attributes that should be cast.
     * This is the crucial part that fixes your problem.
     *
     * @var array
     */
    protected $casts = [
        'finished' => 'boolean',
        'created_at' => 'datetime:d/m/Y',
        'finished_at' => 'datetime:d/m/Y',
        'score' => 'decimal:2',
    ];

    /**
     * Get the user associated with the pivot.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, LessonUser>
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the lesson associated with the pivot.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Lesson, LessonUser>
     */
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Get the formatted creation date (d/m/Y).
     *
     * @return string
     */
    public function getCreatedAtFormattedAttribute(): string
    {
        return Carbon::parse($this->created_at)->format('d/m/Y');
    }

    /**
     * Get the formatted finish date (d/m/Y) or "-" if not set.
     *
     * @return string
     */
    public function getFinishedAtFormattedAttribute(): string
    {
        if (is_null($this->finished_at)) {
            return "-";
        }

        return Carbon::parse($this->finished_at)->format('d/m/Y');
    }

    /**
     * Mutator to parse and set the created_at attribute.
     *
     * @param string $value
     * @return void
     */
    public function setCreatedAtAttribute($value)
    {
        $this->attributes['created_at'] = $this->parseDateAttribute($value);
    }

    /**
     * Mutator to parse and set the finished_at attribute.
     *
     * @param string|null $value
     * @return void
     */
    public function setFinishedAtAttribute($value)
    {
        $this->attributes['finished_at'] = $this->parseDateAttribute($value);
    }

    /**
     * Parse a date string from d/m/Y format to Y-m-d.
     *
     * @param string|null $value
     * @return string|null
     */
    protected function parseDateAttribute($value)
    {
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
            return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
        }

        return $value;
    }

    /**
     * Get the score normalized to a 0-10 scale, or "-" if not set.
     *
     * @param  int|null  $value
     * @return string
     */
    public function getScoreAttribute($value): string
    {
        if (is_null($value)) {
            return "-";
        }

        $normalizedScore = $value / 10.0;

        return number_format($normalizedScore, 1, ',', '');
    }
}
