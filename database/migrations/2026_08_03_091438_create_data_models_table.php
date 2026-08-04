<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');            // Nama tampil, misal "Report Penjualan"
            $table->string('slug')->unique();  // Dipakai di URL, misal "report-penjualan"
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_models');
    }
};