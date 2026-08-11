<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fasilitator_materis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fasilitator_id')->constrained('fasilitators')->cascadeOnDelete();
            $table->foreignId('materi_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['fasilitator_id', 'materi_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fasilitator_materis');
    }
};
