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
        Schema::table('daily_reminders', function (Blueprint $table) {
            $table->time('due_time')->default('09:00:00')->after('due_date');
            $table->timestamp('reminder_1_day_email_sent_at')->nullable()->after('source');
            $table->timestamp('reminder_12_hour_email_sent_at')->nullable()->after('reminder_1_day_email_sent_at');
            $table->timestamp('reminder_3_hour_email_sent_at')->nullable()->after('reminder_12_hour_email_sent_at');
            $table->timestamp('reminder_1_hour_email_sent_at')->nullable()->after('reminder_3_hour_email_sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_reminders', function (Blueprint $table) {
            $table->dropColumn([
                'due_time',
                'reminder_1_day_email_sent_at',
                'reminder_12_hour_email_sent_at',
                'reminder_3_hour_email_sent_at',
                'reminder_1_hour_email_sent_at',
            ]);
        });
    }
};
