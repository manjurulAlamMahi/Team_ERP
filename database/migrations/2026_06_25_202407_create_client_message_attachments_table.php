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
        Schema::create('client_message_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_message_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['last_message', 'attachment']);
            $table->string('original_name')->nullable();
            $table->string('path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_message_attachments');
    }
};
