<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_calendars', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->date('date')->unique();
            $table->enum('type', ['work', 'off', 'holiday']);
            $table->text('description')->nullable();
            $table->boolean('is_national_holiday')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_calendars');
    }
};
