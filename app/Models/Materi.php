<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'mapel_id', 'kelas_id', 'guru_id', 'judul', 'slug',
    'konten', 'file_path', 'video_url', 'is_published',
])]
class Materi extends Model
{
    use SoftDeletes;

    protected $table = 'materis';

    protected function casts(): array
    {
        return [
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
}
