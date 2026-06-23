<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'mapel_id', 'kelas_id', 'guru_id', 'judul', 'deskripsi', 'jenis',
    'durasi_menit', 'waktu_mulai', 'waktu_selesai', 'acak_soal',
    'acak_opsi', 'tampilkan_hasil', 'passcode', 'is_published',
])]
class Ujian extends Model
{
    protected $table = 'ujians';

    protected function casts(): array
    {
        return [
            'waktu_mulai' => 'datetime',
            'waktu_selesai' => 'datetime',
            'durasi_menit' => 'integer',
            'acak_soal' => 'boolean',
            'acak_opsi' => 'boolean',
            'tampilkan_hasil' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function soals(): HasMany
    {
        return $this->hasMany(Soal::class);
    }

    public function ujianHasils(): HasMany
    {
        return $this->hasMany(UjianHasil::class);
    }
}
