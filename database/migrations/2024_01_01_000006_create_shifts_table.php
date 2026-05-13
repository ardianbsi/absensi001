<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 20)->unique();
            $table->enum('type', ['fixed', 'flexible', 'rotating']);
            $table->time('clock_in_time')->nullable();
            $table->time('clock_out_time')->nullable();
            $table->integer('late_tolerance_minutes')->default(15);
            $table->integer('max_early_clock_in')->default(30);
            $table->boolean('is_active')->default(true);
            $table->string('color', 20)->nullable();
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
