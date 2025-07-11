<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuidebookCategory extends Model
{
    /** @use HasFactory<\Database\Factories\GuidebookCategoryFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'icon',
    ];

    /**
     * Get all of the guidebooks for the category.
     */
    public function guidebooks()
    {
        return $this->hasMany(Guidebook::class);
    }
}
