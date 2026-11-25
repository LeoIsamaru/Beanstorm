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
        Schema::create('coffee_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('user_type')->default(4);   // 1 = admin, 4 = user normal
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->unsignedBigInteger('favorite_coffee_id')->nullable(); // pode existir user sem café favorito
            $table->unsignedBigInteger('disliked_coffee_id')->nullable(); // pode existir user sem café que não gosta
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coffee_users');
    }
};
