<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Information extends Model
{
    /** @use HasFactory<\Database\Factories\InformationFactory> */
    use HasFactory, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'published',
        'user_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * This method is used to define how certain attributes should be cast to native types.
     * In this case, it casts the 'published' attribute to a boolean.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published' => 'boolean',
        ];
    }

    /**
     * Get the array representation of the model for indexing.
     *
     * This method returns an array of model attributes to be indexed by the search system.
     * It is used by the Searchable trait to define which fields should be searchable.
     *
     * @return array<string, string>
     */
    public function toSearchableArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
        ];
    }

    /**
     * Get the user that owns the information.
     *
     * This method defines the inverse of the relationship. It indicates that each piece of information
     * belongs to a single user, based on the `user_id` field.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
