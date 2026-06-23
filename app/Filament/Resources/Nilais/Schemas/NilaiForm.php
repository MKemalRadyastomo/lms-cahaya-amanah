<?php

namespace App\Filament\Resources\Nilais\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NilaiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas')->schema([
                    Select::make('siswa_id')
                        ->label('Siswa')
                        ->relationship('siswa', 'name', modifyQueryUsing: fn ($query) => $query->where('role', User::ROLE_SISWA))
                        ->required()->searchable()->preload(),
                    Select::make('mapel_id')
                        ->label('Mata Pelajaran')
                        ->relationship('mapel', 'nama')
                        ->required()->searchable()->preload(),
                    Select::make('kelas_id')
                        ->label('Kelas')
                        ->relationship('kelas', 'nama')
                        ->required()->searchable()->preload(),
                    Select::make('tahun_ajaran_id')
                        ->label('Tahun Ajaran')
                        ->relationship('tahunAjaran', 'tahun')
                        ->required()->searchable()->preload(),
                    Select::make('semester')
                        ->required()
                        ->options(['ganjil' => 'Ganjil', 'genap' => 'Genap']),
                ])->columns(2),

                Section::make('Nilai')->schema([
                    TextInput::make('nilai_tugas')->numeric()->minValue(0)->maxValue(100)->label('Nilai Tugas'),
                    TextInput::make('nilai_ujian')->numeric()->minValue(0)->maxValue(100)->label('Nilai Ujian'),
                    TextInput::make('nilai_akhir')->numeric()->minValue(0)->maxValue(100)->label('Nilai Akhir')->helperText('Bisa diisi manual atau dihitung otomatis nanti.'),
                    Select::make('predikat')
                        ->options([
                            'A' => 'A (Sangat Baik)',
                            'B' => 'B (Baik)',
                            'C' => 'C (Cukup)',
                            'D' => 'D (Kurang)',
                            'E' => 'E (Buruk)',
                        ]),
                    Textarea::make('deskripsi')->label('Deskripsi Rapor')->columnSpanFull(),
                ])->columns(2),
            ]);
    }
}
