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
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('nim')->unique();
            $table->string('nama', 150);
            $table->string('fakultas', 50)->nullable();
            $table->string('prodi', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->integer('jumlah_sks')->nullable();
            $table->string('no_hp', 15)->nullable();
            $table->string('no_hp_wali', 15)->nullable();
            $table->enum('jenis_kelamin', ['P','L'])->nullable();
            $table->enum('ukuran_jacket_rompi', ['S','M', 'L', 'XL', 'XXL', '3XL'])->nullable();
            $table->enum('punya_kendaraan', ['Punya', 'Tidak'])->nullable();
            $table->enum('tipe_kendaraan', ['Tidak Ada', 'Mobil', 'Sepeda Motor'])->nullable();
            $table->enum('punya_lisensi', ['Tidak Ada', 'SIM A', 'SIM C', 'SIM B'])->nullable();
            $table->string('keahlian',100)->nullable();
            $table->enum('status_kkn', ['Belum Daftar','Sudah Daftar'])->default('Belum Daftar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
