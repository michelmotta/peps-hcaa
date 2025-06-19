<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Doubt extends Model
{
    /** @use HasFactory<\Database\Factories\DoubtFactory> */
    use HasFactory, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'lesson_id',
        'user_id',
        'doubt',
        'description',
        'answered',
        'answered_at',
    ];

    public function toSearchableArray()
    {
        return [
            'doubt' => $this->doubt,
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
     * Get the formatted creation date (d/m/Y).
     *
     * @return string
     */
    public function getAnsweredAtFormattedAttribute(): string
    {
        if ($this->answered_at) {
            return Carbon::parse($this->answered_at)->format('d/m/Y');
        }

        return '';
    }

    /**
     * Get the user associated with the pivot.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, LessonUser>
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
