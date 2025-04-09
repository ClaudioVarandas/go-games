<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('game_lists', function (Blueprint $table) {
            $table->enum('type', ['regular', 'backlog', 'wishlist'])
                ->default('regular')
                ->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('game_lists', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
