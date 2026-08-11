<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengajuan extends Model
{
    use HasFactory;

    protected $fillable = ['fasilitator_id', 'kegiatan_id', 'nama_kegiatan', 'materi', 'tanggal', 'penyelenggara_id', 'lokasi', 'peran', 'status', 'catatan_admin', 'reviewed_by', 'submitted_at', 'reviewed_at'];

    protected function casts(): array
    {
        return ['tanggal' => 'date', 'submitted_at' => 'datetime', 'reviewed_at' => 'datetime'];
    }

    public function fasilitator(): BelongsTo { return $this->belongsTo(Fasilitator::class); }
    public function kegiatan(): BelongsTo { return $this->belongsTo(Kegiatan::class); }
    public function penyelenggara(): BelongsTo { return $this->belongsTo(Penyelenggara::class); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }
}
