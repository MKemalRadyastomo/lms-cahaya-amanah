<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['ujian_id', 'tipe', 'pertanyaan', 'opsi', 'jawaban_benar', 'poin'])]
class Soal extends Model
{
    protected $table = 'soals';

    protected function casts(): array
    {
        return [
            'opsi' => 'array',
            'poin' => 'decimal:2',
        ];
    }

    public function ujian(): BelongsTo
    {
        return $this->belongsTo(Ujian::class);
    }
}
