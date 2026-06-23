<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['judul', 'konten', 'target_type', 'target_id', 'created_by', 'is_pinned'])]
class Pengumuman extends Model
{
    protected $table = 'pengumuman';

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Pengumuman yang terlihat untuk seorang siswa.
     */
    public function scopeVisibleToSiswa(Builder $query, User $siswa, int $kelasId): Builder
    {
        return $query->where(function (Builder $q) use ($siswa, $kelasId) {
            $q->where('target_type', 'semua')
                ->orWhere(function (Builder $q2) use ($kelasId) {
                    $q2->where('target_type', 'kelas')->where('target_id', $kelasId);
                })
                ->orWhere(function (Builder $q2) use ($siswa) {
                    $q2->where('target_type', 'siswa')->where('target_id', $siswa->id);
                });
        });
    }
}
