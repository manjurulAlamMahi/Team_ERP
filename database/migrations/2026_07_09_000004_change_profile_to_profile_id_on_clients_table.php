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
        Schema::table('clients', function (Blueprint $table) {
            $table->dropUnique(['profile', 'username']);
            $table->dropColumn('profile');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->foreignId('profile_id')->after('username')->constrained('fiverr_profiles')->restrictOnDelete();
            $table->unique(['profile_id', 'username']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropUnique(['profile_id', 'username']);
            $table->dropConstrainedForeignId('profile_id');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->string('profile')->after('username');
            $table->unique(['profile', 'username']);
        });
    }
};
