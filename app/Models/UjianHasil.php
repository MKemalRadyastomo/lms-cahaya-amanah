<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'ujian_id', 'siswa_id', 'jawaban', 'nilai', 'status',
    'waktu_mulai', 'waktu_selesai',
])]
class UjianHasil extends Model
{
    protected $table = 'ujian_hasils';

    protected function casts(): array
    {
        return [
            'jawaban' => 'array',
            'nilai' => 'decimal:2',
            'waktu_mulai' => 'datetime',
            'waktu_selesai' => 'datetime',
        ];
    }

    public function ujian(): BelongsTo
    {
        return $this->belongsTo(Ujian::class);
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }
}
