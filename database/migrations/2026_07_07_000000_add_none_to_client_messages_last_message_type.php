<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE client_messages MODIFY last_message_type ENUM('none', 'image', 'multiple') DEFAULT 'image'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE client_messages MODIFY last_message_type ENUM('image', 'multiple') DEFAULT 'image'");
    }
};
