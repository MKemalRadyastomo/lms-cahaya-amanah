<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_id')->constrained()->cascadeOnDelete();
            $table->enum('tipe', ['pg', 'essay'])->default('pg');
            $table->text('pertanyaan');
            $table->json('opsi')->nullable(); // array pilihan ganda
            $table->text('jawaban_benar')->nullable();
            $table->decimal('poin', 5, 2)->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soals');
    }
};
