<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_pelatihans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fasilitator_id')->constrained()->onDelete('cascade');
            $table->enum('kategori', ['materi_diajarkan', 'pelatihan_terkait', 'pengalaman_mengajar']);
            $table->string('nama_kegiatan');
            $table->string('penyelenggara')->nullable();
            $table->date('tanggal')->nullable();
            $table->string('sertifikat')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_pelatihans');
    }
};