<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->unsignedInteger('order')->default(0)->after('completed');
        });

        // Backfill existing rows so each user's tasks keep their current (creation) order.
        DB::table('todos')->select('id', 'user_id')->orderBy('created_at')->get()
            ->groupBy('user_id')
            ->each(function ($rows) {
                foreach ($rows->values() as $index => $row) {
                    DB::table('todos')->where('id', $row->id)->update(['order' => $index]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
