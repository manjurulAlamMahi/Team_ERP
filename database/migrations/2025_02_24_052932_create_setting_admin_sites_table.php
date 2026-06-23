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
        Schema::create('setting_admin_sites', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo')->default('admin/assets/images/logo/logo.png');
            $table->string('logo_dark')->default('admin/assets/images/logo/logo-dark.png'); // Changed
            $table->string('logo_sm')->default('admin/assets/images/logo/logo-sm.png'); // Changed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_admin_sites');
    }
};
