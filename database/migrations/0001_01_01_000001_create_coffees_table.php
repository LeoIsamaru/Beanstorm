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
        Schema::create('coffees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('photo')->nullable();    // pode existir café sem foto
            $table->string('caffeine');                       // lista: Arabica, Robusta, Liberica...
            $table->string('origin_country');
            $table->string('roast_level');                   // lista: light, medium, dark
            $table->timestamps();
            $table->unsignedBigInteger('user_id')->nullable();  // pode existir café sem user atribuido
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coffees');
    }
};
