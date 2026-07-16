<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE daily_issues MODIFY issue TEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE daily_issues MODIFY issue TEXT NOT NULL');
    }
};
