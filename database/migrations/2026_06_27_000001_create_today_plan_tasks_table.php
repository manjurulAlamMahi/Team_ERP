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
        Schema::create('today_plan_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('plan_date');
            $table->string('client_name');
            $table->string('profile_name');
            $table->text('details');
            $table->enum('source', ['planned', 'leader_assigned', 'personal'])->default('planned');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('review_comment')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->boolean('leader_verified')->nullable();
            $table->text('completion_comment')->nullable();
            $table->timestamps();

            $table->index(['team_id', 'plan_date']);
            $table->index(['user_id', 'plan_date']);
            $table->index(['team_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('today_plan_tasks');
    }
};
