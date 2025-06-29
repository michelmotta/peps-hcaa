<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
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
        'file_id',
        'parent_id',
    ];

    /**
     * Convert the model instance to an array for indexing/search.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'parent_id' => $this->parent_id,
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

    public function parent()
    {
        return $this->belongsTo(Specialty::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Specialty::class, 'parent_id');
    }
}
