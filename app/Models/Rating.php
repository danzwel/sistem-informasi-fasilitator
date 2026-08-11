<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = ['kegiatan_id', 'fasilitator_id', 'reviewer_id', 'rating', 'review'];

    public function kegiatan(): BelongsTo { return $this->belongsTo(Kegiatan::class); }
    public function fasilitator(): BelongsTo { return $this->belongsTo(Fasilitator::class); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewer_id'); }
}
