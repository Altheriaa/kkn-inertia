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
        Schema::create('pendaftaran_kkn', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->unique()->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained('payment')->onDelete('set null');
            $table->foreignId('jadwal_kkn_id')->nullable()->constrained('jadwal_kkn')->onDelete('cascade');
            $table->foreignId('kelompok_kkn_id')->nullable()->constrained('kelompok_kkn')->onDelete('set null');
            $table->integer('jenis_kkn_id');
            $table->string('jenis_kkn');
            $table->enum('status_pendaftaran', ['pending', 'valid', 'ditolak'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_kkn');
    }
};
