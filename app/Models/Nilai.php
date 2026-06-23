<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'siswa_id', 'mapel_id', 'kelas_id', 'tahun_ajaran_id', 'semester',
    'nilai_tugas', 'nilai_ujian', 'nilai_akhir', 'predikat', 'deskripsi',
])]
class Nilai extends Model
{
    protected $table = 'nilais';

    protected function casts(): array
    {
        return [
            'nilai_tugas' => 'decimal:2',
            'nilai_ujian' => 'decimal:2',
            'nilai_akhir' => 'decimal:2',
        ];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }
}
