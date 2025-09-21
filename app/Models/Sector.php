<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Sector extends Model
{
    /** @use HasFactory<\Database\Factories\LibraryFactory> */
    use HasFactory, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name'
    ];

    /**
     * Convert the model instance to an array for indexing/search.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray()
    {
        return [
            'name' => $this->name
        ];
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_id');
    }
}
