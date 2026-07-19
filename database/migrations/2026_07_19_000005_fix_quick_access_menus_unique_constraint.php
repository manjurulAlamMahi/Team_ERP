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
        Schema::table('quick_access_menus', function (Blueprint $table) {
            // route was globally unique, so two different users could never
            // quick-access the same page - the second insert threw a 500.
            $table->dropUnique(['route']);
            $table->unique(['user_id', 'route']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quick_access_menus', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'route']);
            $table->unique(['route']);
        });
    }
};
