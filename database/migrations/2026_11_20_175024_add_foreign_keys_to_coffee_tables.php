<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coffee_users', function (Blueprint $table) {
			$table->foreign('favorite_coffee_id')->references('id')->on('coffees')->onDelete('set null'); // o 'favorite_coffee_id' (FK) aponta para a coluna 'id' da tabela 'coffees'. Se eliminar um café, o utilizador não é eliminado
            $table->foreign('disliked_coffee_id')->references('id')->on('coffees')->onDelete('set null'); // o 'disliked_coffee_id' (FK) aponta para a coluna 'id' da tabela 'coffees'. Se eliminar um café, o utilizador não é eliminado
        });

        Schema::table('coffees', function (Blueprint $table) {
			$table->foreign('user_id')->references('id')->on('coffee_users')->onDelete('cascade'); // o 'user_id' (FK) aponta para a coluna 'id' da tabela 'coffee_users'. Se o user for eliminado, os seus cafes também o são
        });
    }

    public function down(): void
    {
        Schema::table('coffees', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('coffee_users', function (Blueprint $table) {
            $table->dropForeign(['favorite_coffee_id']);
            $table->dropForeign(['disliked_coffee_id']);
        });
    }
};
