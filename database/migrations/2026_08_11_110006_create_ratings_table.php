<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fasilitator_id')->constrained('fasilitators')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users');
            $table->unsignedTinyInteger('rating');
            $table->text('review')->nullable();
            $table->timestamps();
            $table->unique(['kegiatan_id', 'fasilitator_id', 'reviewer_id'], 'ratings_kegiatan_fasilitator_reviewer_unique');
            $table->index(['fasilitator_id', 'rating']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
