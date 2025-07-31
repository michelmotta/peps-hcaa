<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Illuminate\Support\Str;

class Message extends Model
{
    /** @use HasFactory<\Database\Factories\SuggestionFactory> */
    use HasFactory, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'subject',
        'description',
        'lesson_id',
        'user_id',
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
            'subject' => $this->name,
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
     * Get the user associated with the suggestion.
     *
     * Defines an inverse one-to-many relationship where each suggestion
     * belongs to one user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, Suggestion>
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get a short version of the description.
     *
     * @return string
     */
    public function getDescriptionResumeAttribute(): string
    {
        return Str::limit($this->description, 100);
    }
}
