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
            $table->foreignId('parent_id')->nullable()->constrained('menus')->nullOnDelete();
            $table->string('label');
            $table->string('icon')->nullable();       // nama icon lucide, mis. "LayoutDashboard"
            $table->string('type')->default('link');   // 'link' (url manual) atau 'page' (halaman dari builder)
            $table->string('route')->nullable();        // dipakai kalau type = link
            $table->foreignId('page_id')->nullable();   // dipakai kalau type = page (fase 2)
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};