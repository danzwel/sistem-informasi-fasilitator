<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fasilitator_id')->constrained('fasilitators')->cascadeOnDelete();
            $table->foreignId('kegiatan_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nama_kegiatan');
            $table->string('materi')->nullable();
            $table->date('tanggal')->nullable();
            $table->foreignId('penyelenggara_id')->nullable()->constrained()->nullOnDelete();
            $table->string('lokasi')->nullable();
            $table->string('peran')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->text('catatan_admin')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->index(['fasilitator_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuans');
    }
};
