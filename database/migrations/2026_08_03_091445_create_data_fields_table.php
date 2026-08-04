<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_model_id')->constrained('data_models')->cascadeOnDelete();
            $table->string('name');            // Nama tampil, misal "Judul Laporan"
            $table->string('key');             // Dipakai sebagai key JSON, misal "judul_laporan"
            $table->string('type');            // text, number, date, textarea, boolean, select
            $table->json('options')->nullable(); // buat type "select": daftar pilihan
            $table->boolean('is_required')->default(false);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->unique(['data_model_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_fields');
    }
};