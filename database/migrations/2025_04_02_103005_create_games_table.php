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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('igdb_id')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('cover_url')->nullable();
            $table->date('release_date')->nullable();
            $table->text('summary')->nullable();
            $table->decimal('rating', 3, 1)->nullable();
            $table->json('genres')->nullable();
            $table->json('platforms')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
