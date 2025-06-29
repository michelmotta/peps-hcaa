<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Guidebook extends Model
{
    /** @use HasFactory<\Database\Factories\GuidebookFactory> */
    use HasFactory, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description'
    ];

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
}
