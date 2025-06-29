<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Library extends Model
{
    /** @use HasFactory<\Database\Factories\LibraryFactory> */
    use HasFactory, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'file_id',
        'user_id',
    ];

    /**
     * Convert the model instance to an array for indexing/search.
     *
     * Returns an array of attributes that should be indexed by the search engine.
     * Used by the `Searchable` trait.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray()
    {
        return [
            'title' => $this->title
        ];
    }

    /**
     * Get the file associated with the specialty.
     *
     * Defines an inverse one-to-one or many relationship to File.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<File, Specialty>
     */
    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    /**
     * Get the user who created the specialty.
     *
     * Defines an inverse one-to-many relationship to User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, Specialty>
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the formatted creation date.
     *
     * This is a custom accessor that returns the `created_at` timestamp
     * formatted as `dd/mm/yyyy`.
     *
     * @return string Formatted creation date.
     */
    public function getCreatedAtFormattedAttribute(): string
    {
        return Carbon::parse($this->created_at)->format('d/m/Y');
    }

}
