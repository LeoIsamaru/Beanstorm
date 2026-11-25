<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;

#[Fillable(['name', 'email', 'password', 'user_type', 'favorite_brand_id', 'disliked_brand_id', 'favorite_coffee_id', 'disliked_coffee_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // Tabela usada para os users
    protected $table = 'coffee_users';

    // User type constantes baseadas na "user_type" coluna da BD
    const TYPE_ADMIN = 1;
    const TYPE_GENERIC_USER = 4;



    // verifica se o user é um admin
    public function isAdmin(): bool
    {
        return (int) $this->user_type === self::TYPE_ADMIN;
    }


    // verifica se o user é generico/normal
    public function isGenericUser(): bool
    {
        return (int) $this->user_type === self::TYPE_GENERIC_USER;
    }


    // Relação da marca preferida
    public function favoriteBrand()
    {
        return $this->belongsTo(Brand::class, 'favorite_brand_id');
    }


    // Relação da marca que não gosta
    public function dislikedBrand()
    {
        return $this->belongsTo(Brand::class, 'disliked_brand_id');
    }



    // Relação de reserva se a coluna não foi renomeada de favorite_coffee_id
    public function favoriteBrandLegacy()
    {
        return $this->belongsTo(Brand::class, 'favorite_coffee_id');
    }



        // Relação de reserva se a coluna não foi renomeada de disliked_coffee_id

    public function dislikedBrandLegacy()
    {
        return $this->belongsTo(Brand::class, 'disliked_coffee_id');
    }


// Recebe um join do nome da marca com o batch para a marca favorita
    public function getFavoriteBrandDisplayAttribute(): string
    {
        if (isset($this->attributes['favorite_brand_display']) && $this->attributes['favorite_brand_display'] !== null) {
            return $this->attributes['favorite_brand_display'];
        }
        $brand = $this->favoriteBrand ?? $this->favoriteBrandLegacy ?? null;
        return $brand ? "{$brand->name} - {$brand->batch}" : '-';
    }


    // Recebe um join do nome da marca com o batch para a marca que não gosta
    public function getDislikedBrandDisplayAttribute(): string
    {
        if (isset($this->attributes['disliked_brand_display']) && $this->attributes['disliked_brand_display'] !== null) {
            return $this->attributes['disliked_brand_display'];
        }
        $brand = $this->dislikedBrand ?? $this->dislikedBrandLegacy ?? null;
        return $brand ? "{$brand->name} - {$brand->batch}" : '-';
    }

    /*
     * Obtem os atributos que devem ser cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
