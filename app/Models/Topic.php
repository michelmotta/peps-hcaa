<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Topic extends Model
{
    /** @use HasFactory<\Database\Factories\TopicFactory> */
    use HasFactory, Searchable;

    protected $casts = [
        'attachments' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'video_id',
        'lesson_id',
        'resume',
        'description',
        'attachments',
    ];

    /**
     * Convert the model instance to an array for indexing/search.
     *
     * This method returns an array representation of the model's attributes 
     * that should be indexed by the search engine. It's used by the 
     * `Searchable` trait to index the model's searchable fields.
     *
     * @return array<string, mixed> Array of searchable attributes.
     */
    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
        ];
    }

    /**
     * Get the lesson associated with the topic.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Lesson, Topic>
     */
    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    /**
     * Get the video associated with the topic.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Video, Topic>
     */
    public function video()
    {
        return $this->belongsTo(Video::class, 'video_id');
    }

    /**
     * Get the quizzes that belong to this topic.
     *
     * @return HasMany<Quiz>
     */
    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'topic_id');
    }

    /**
     * Always return the attachments attribute as a Collection.
     *
     * @param string|array|null $value
     * @return \Illuminate\Support\Collection
     */
    public function getAttachmentsAttribute($value)
    {
        $data = is_array($value) ? $value : json_decode($value, true);

        return collect($data ?? []);
    }
}
