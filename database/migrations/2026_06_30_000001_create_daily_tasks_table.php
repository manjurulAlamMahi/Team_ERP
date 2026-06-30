<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();       // assigned to
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete(); // who made it
            $table->date('task_date');
            $table->string('client_name');
            $table->string('profile_name');
            $table->text('plan_details');
            $table->date('expected_complete_date')->nullable();
            // self / leader / co_leader / stack_lead
            $table->string('source')->default('self');
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('remarks_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('remarks_updated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_tasks');
    }
};
