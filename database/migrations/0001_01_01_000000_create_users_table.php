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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Employee Information
            $table->string('employee_id', 20)->unique();

            // Profile
            $table->string('avatar')->default('user/avatar/avatar.png');
            $table->string('cover')->default('user/cover_banner/1.jpg');

            // Basic Information
            $table->string('username', 250)->unique();
            $table->string('name', 250);

            // Contact
            $table->string('email')->unique(); // Personal Email
            $table->string('official_email')->nullable()->unique();

            $table->string('phone_code', 5)->nullable();
            $table->string('phone', 20)->nullable()->unique();

            $table->string('telegram')->nullable();
            $table->string('github')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('facebook')->nullable();
            $table->string('whatsapp')->nullable();

            // Organization
            $table->foreignId('stack_id')
                ->nullable()
                ->constrained('stacks')
                ->nullOnDelete();

            $table->foreignId('team_id')
                ->nullable()
                ->constrained('teams')
                ->nullOnDelete();

            $table->foreignId('community_id')
                ->nullable()
                ->constrained('communities')
                ->nullOnDelete();

            $table->foreignId('reporting_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('designation')->nullable();

            // Employment
            $table->date('dob')->nullable();
            $table->date('joining_date')->nullable();
            $table->date('probation_end_date')->nullable();

            $table->enum('employment_status', [
                'probation',
                'active',
                'resigned',
                'terminated'
            ])->default('active');

            $table->string('weekend', 20)->default('friday');

            // Address
            $table->text('address')->nullable();

            // Authentication
            $table->timestamp('email_verified_at')->nullable();
            $table->string('otp')->nullable();
            $table->string('password');

            // System
            $table->enum('status', [
                'active',
                'inactive'
            ])->default('active');

            $table->boolean('is_admin')
                ->default(false)
                ->comment('Super Admin Access');

            $table->boolean('is_request')
                ->default(false)
                ->comment('Pending Registration Request');

            $table->foreignId('added_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
