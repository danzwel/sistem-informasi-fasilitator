<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatPendidikan extends Model
{
    use HasFactory;

    protected $fillable = [
        'fasilitator_id',
        'jenjang',
        'institusi',
        'kota',
        'tahun_mulai',
        'tahun_selesai',
    ];

    public function fasilitator()
    {
        return $this->belongsTo(Fasilitator::class);
    }
}