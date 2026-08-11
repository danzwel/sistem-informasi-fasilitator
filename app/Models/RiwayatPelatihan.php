<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}