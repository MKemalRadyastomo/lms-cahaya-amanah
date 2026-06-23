<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ujian_hasils', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_id')->constrained()->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('users')->cascadeOnDelete();
            $table->json('jawaban')->nullable(); // { soal_id: jawaban_siswa }
            $table->decimal('nilai', 5, 2)->nullable();
            $table->enum('status', ['belum_mulai', 'sedang', 'selesai'])->default('belum_mulai');
            $table->dateTime('waktu_mulai')->nullable();
            $table->dateTime('waktu_selesai')->nullable();
            $table->timestamps();

            $table->unique(['ujian_id', 'siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ujian_hasils');
    }
};
