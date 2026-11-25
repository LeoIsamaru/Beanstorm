<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'batch', 'price', 'caffeine', 'coffee_id'])]
class Brand extends Model
{
    use HasFactory;

    protected $table = 'brands';

    /**
     * Get the coffee bean that owns this brand.
     */
    public function coffee()
    {
        return $this->belongsTo(Coffee::class, 'coffee_id');
    }

    /**
     * Helper to get joined brand name and batch.
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->name} - {$this->batch}";
    }
}
