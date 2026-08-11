<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class RiwayatPelatihan extends Model
{
    use HasFactory;

    protected $fillable = [
        'fasilitator_id',
        'kategori',
        'nama_kegiatan',
        'penyelenggara',
        'tanggal',
        'sertifikat',
        'keterangan',
    ];

    public function fasilitator()
    {
        return $this->belongsTo(Fasilitator::class);
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }
}
