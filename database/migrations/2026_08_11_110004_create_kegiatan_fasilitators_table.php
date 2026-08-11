<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kegiatan_fasilitators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fasilitator_id')->constrained('fasilitators')->cascadeOnDelete();
            $table->string('peran')->nullable();
            $table->timestamps();
            $table->unique(['kegiatan_id', 'fasilitator_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kegiatan_fasilitators');
    }
};
