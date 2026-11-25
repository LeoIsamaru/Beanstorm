<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove coluna caffeine da tabela coffees (passou para a tabela brands, na migration anterior)
        Schema::table('coffees', function (Blueprint $table) {
            if (Schema::hasColumn('coffees', 'caffeine')) {
                $table->dropColumn('caffeine');
            }
        });

        // Atualiza FK's e colunas da tabela coffee_users para apontar para a nova tabela brands
        Schema::table('coffee_users', function (Blueprint $table) {
            $table->dropForeign(['favorite_coffee_id']);
            $table->dropForeign(['disliked_coffee_id']);

            $table->renameColumn('favorite_coffee_id', 'favorite_brand_id');
            $table->renameColumn('disliked_coffee_id', 'disliked_brand_id');
        });

        Schema::table('coffee_users', function (Blueprint $table) {
            $table->foreign('favorite_brand_id')->references('id')->on('brands')->onDelete('set null'); // o 'favorite_brand_id' (FK) aponta para a coluna 'id' da tabela 'brands'. Se eliminar um café, o utilizador não é eliminado
            $table->foreign('disliked_brand_id')->references('id')->on('brands')->onDelete('set null'); // o 'disliked_brand_id' (FK) aponta para a coluna 'id' da tabela 'brands'. Se eliminar um café, o utilizador não é eliminado
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coffee_users', function (Blueprint $table) {
            $table->dropForeign(['favorite_brand_id']); // esquece a FK
            $table->dropForeign(['disliked_brand_id']); // esquece a FK

            $table->renameColumn('favorite_brand_id', 'favorite_coffee_id'); // renomeia coluna
            $table->renameColumn('disliked_brand_id', 'disliked_coffee_id'); // renomeia coluna
        });

        Schema::table('coffee_users', function (Blueprint $table) {
            $table->foreign('favorite_coffee_id')->references('id')->on('coffees')->onDelete('set null'); // o 'favorite_coffee_id' (FK) aponta para a coluna 'id' da tabela 'coffees'. Se o user for eliminado, os seus cafes passam a null
            $table->foreign('disliked_coffee_id')->references('id')->on('coffees')->onDelete('set null');
        });

        Schema::table('coffees', function (Blueprint $table) {
            $table->string('caffeine')->nullable();
        });
    }
};

