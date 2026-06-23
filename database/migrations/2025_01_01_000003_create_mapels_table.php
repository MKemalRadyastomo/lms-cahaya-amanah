<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mapels', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique(); // contoh: MAT
            $table->string('nama'); // contoh: Matematika
            $table->string('jenjang')->nullable(); // contoh: X, XI, XII, umum
            $table->unsignedTinyInteger('kkm')->default(70);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mapels');
    }
};
