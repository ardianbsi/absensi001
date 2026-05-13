<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_group_id')->constrained()->onDelete('cascade');
            $table->string('name', 100);
            $table->string('icon', 100)->nullable();
            $table->string('route')->nullable();
            $table->string('url')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('menus')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('parent_id');
            $table->index('menu_group_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
