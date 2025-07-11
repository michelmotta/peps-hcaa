<?php

namespace App\Models;

use App\Enums\GuidebookEnum;
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
        'type',
        'description',
        'guidebook_category_id',
    ];

    /**
     * This tells Laravel to treat the 'type' column as a GuidebookType Enum.
     */
    protected $casts = [
        'type' => GuidebookEnum::class,
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

    /**
     * Get the category that the guidebook belongs to.
     */
    public function category()
    {
        return $this->belongsTo(GuidebookCategory::class, 'guidebook_category_id');
    }
}
