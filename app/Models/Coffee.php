<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


#[Fillable(['species', 'photo', 'origin_country', 'roast_level', 'user_id'])]

class Coffee extends Model
{
    use HasFactory;

    protected $table = 'coffees';


    // Obtem as marcas associadas ao grão de café
    public function brands()
    {
        return $this->hasMany(Brand::class, 'coffee_id');
    }


    // Obtem o user associado ao grão de café
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    // Filtra e devolve os cafés associados a um utilizador específico.
    public function scopeCoffeeByUser($query, $user)
    {
        return $query->where('user_id', $user);
    }
}
