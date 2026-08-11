<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function materis(): BelongsToMany
    {
        return $this->belongsToMany(Materi::class, 'fasilitator_materis')->withTimestamps();
    }

    public function kegiatans(): BelongsToMany
    {
        return $this->belongsToMany(Kegiatan::class, 'kegiatan_fasilitators')->withPivot('peran')->withTimestamps();
    }

    public function pengajuans(): HasMany
    {
        return $this->hasMany(Pengajuan::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }
}
