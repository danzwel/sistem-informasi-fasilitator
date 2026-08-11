<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Fasilitator extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'gelar',
        'nik',
        'nip',
        'pangkat',
        'jabatan',
        'unit_kerja',
        'alamat_kantor',
        'alamat_rumah',
        'no_hp',
        'email',
        'foto',
        'ttd',
        'status',
        'catatan',
    ];

    public function riwayatPelatihan()
    {
        return $this->hasMany(RiwayatPelatihan::class);
    }

    public function riwayatPendidikan()
    {
        return $this->hasMany(RiwayatPendidikan::class);
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }
}
