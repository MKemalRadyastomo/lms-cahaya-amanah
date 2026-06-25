<?php

namespace App\Filament\Resources\UjianHasils\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\View;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UjianHasilForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Identitas')->schema([
                    Placeholder::make('siswa')
                        ->label('Siswa')
                        ->content(fn ($record) => $record?->siswa?->name ?? '-'),
                    Placeholder::make('ujian')
                        ->label('Ujian')
                        ->content(fn ($record) => $record?->ujian?->judul ?? '-'),
                    Placeholder::make('mapel')
                        ->label('Mata Pelajaran')
                        ->content(fn ($record) => $record?->ujian?->mapel?->nama ?? '-'),
                    Placeholder::make('status')
                        ->label('Status')
                        ->content(fn ($record) => ucfirst(str_replace('_', ' ', (string) ($record?->status ?? '-')))),
                    Placeholder::make('waktu_mulai')
                        ->label('Waktu Mulai')
                        ->content(fn ($record) => $record?->waktu_mulai?->format('d M Y, H:i') ?? '-'),
                    Placeholder::make('waktu_selesai')
                        ->label('Waktu Selesai')
                        ->content(fn ($record) => $record?->waktu_selesai?->format('d M Y, H:i') ?? '-'),
                ])->columns(3),

                Section::make('Penilaian')->schema([
                    TextInput::make('nilai')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->label('Nilai Akhir')
                        ->helperText('Nilai PG terhitung otomatis saat siswa mengumpulkan. Sesuaikan manual jika ada soal essay yang perlu dinilai.'),
                ])->columnSpanFull(),

                Section::make('Jawaban Siswa')->schema([
                    View::make('filament.forms.ujian-jawaban'),
                ])->columnSpanFull(),
            ]);
    }
}
