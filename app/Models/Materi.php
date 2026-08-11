<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Materi extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'deskripsi', 'status'];

    public function fasilitators(): BelongsToMany
    {
        return $this->belongsToMany(Fasilitator::class, 'fasilitator_materis')->withTimestamps();
    }
}
