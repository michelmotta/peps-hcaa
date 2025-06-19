<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Specialty extends Model
{
    /** @use HasFactory<\Database\Factories\SpecialtyFactory> */
    use HasFactory, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
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
            'name' => $this->name,
            'description' => $this->description,
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
}
