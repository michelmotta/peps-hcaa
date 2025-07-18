<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Lesson extends Model
{
    /** @use HasFactory<\Database\Factories\LessonFactory> */
    use HasFactory, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'workload',
        'file_id',
        'user_id',
    ];

    /**
     * Convert the model instance to an array for indexing/search.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
        ];
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
     * Get the file associated with the specialty.
     *
     * This method defines a one-to-many relationship between the `Specialty` model 
     * and the `File` model. A specialty may have one associated file.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    /**
     * Get the user associated with the specialty.
     *
     * This method defines a one-to-many relationship between the `Specialty` model 
     * and the `User` model. A specialty is associated with one user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subscriptions()
    {
        return $this->belongsToMany(User::class)
            ->using(LessonUser::class)
            ->withPivot(['score', 'finished', 'finished_at', 'created_at'])
            ->withTimestamps();
    }

    public function completedSubscriptions()
    {
        return $this->belongsToMany(User::class, 'lesson_user')->wherePivot('finished', true);
    }

    /**
     * Get the topics that belong to this lesson.
     *
     * @return HasMany<Topic>
     */
    public function topics()
    {
        return $this->hasMany(Topic::class, 'lesson_id');
    }

    /**
     * Get the doubts that belong to this lesson.
     *
     * @return HasMany<Doubt>
     */
    public function doubts()
    {
        return $this->hasMany(Doubt::class, 'lesson_id');
    }

    /**
     * Get the doubts that belong to this lesson.
     *
     * @return HasMany<Doubt>
     */
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'lesson_id');
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'lesson_id');
    }

    /**
     * Get only the answered doubts for this lesson.
     */
    public function answeredDoubts()
    {
        return $this->hasMany(Doubt::class)->where('answered', true);
    }

    public function specialties()
    {
        return $this->belongsToMany(Specialty::class, 'lesson_specialty');
    }

    /**
     * Get the total duration of all videos in the lesson topics.
     *
     * @return string
     */
    public function getTotalDurationAttribute(): string
    {
        $totalSeconds = $this->topics
            ->filter(fn($topic) => $topic->video && $topic->video->duration)
            ->reduce(function ($carry, $topic) {
                [$h, $m, $s] = array_pad(explode(':', $topic->video->duration), 3, 0);
                return $carry + ($h * 3600) + ($m * 60) + $s;
            }, 0);

        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

        return sprintf('%dh %dm %ds', $hours, $minutes, $seconds);
    }
}
