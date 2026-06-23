<?php

namespace App\Filament\Resources\PengumpulanTugas\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class PengumpulanTugasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pengumpulan')->schema([
                    fn (Get $get, ?Model $record) => self::infoField('Siswa', $record?->siswa?->name ?? '-'),
                    fn (Get $get, ?Model $record) => self::infoField('Tugas', $record?->tugas?->judul ?? '-'),
                    fn (Get $get, ?Model $record) => self::infoField('Mata Pelajaran', $record?->tugas?->mapel?->nama ?? '-'),
                    fn (Get $get, ?Model $record) => self::infoField('Waktu Pengumpulan', $record?->submitted_at?->isoFormat('D MMM Y, HH:mm') ?? '-'),
                    fn (Get $get, ?Model $record) => self::infoField('Status', ucfirst($record?->status ?? '-')),
                    fn (Get $get, ?Model $record) => self::fileField($record),
                ])->columns(2),

                Section::make('Catatan Siswa')
                    ->schema([
                        fn (Get $get, ?Model $record) => self::catatanField($record?->catatan),
                    ])
                    ->collapsible(),

                Section::make('Penilaian')->schema([
                    TextInput::make('nilai')
                        ->label('Nilai')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(fn (Get $get, ?Model $record) => $record?->tugas?->poin_max ?? 100)
                        ->helperText(fn (Get $get, ?Model $record) => 'Skala 0 - '.($record?->tugas?->poin_max ?? 100)),
                    Textarea::make('feedback')
                        ->label('Umpan Balik / Catatan Nilai')
                        ->rows(3)
                        ->columnSpanFull(),
                ])->columns(2),
            ]);
    }

    private static function infoField(string $label, string $value): Placeholder
    {
        return Placeholder::make($label)
            ->label($label)
            ->content($value);
    }

    private static function fileField(?Model $record): Placeholder
    {
        $content = 'Tidak ada berkas';

        if ($record && $record->file_path) {
            $disk = Storage::disk(config('filesystems.default'));
            $exists = $disk->exists($record->file_path);
            $name = basename($record->file_path);

            if ($exists) {
                $url = route('admin.pengumpulan.unduh', $record);
                $content = new HtmlString(
                    '<a href="'.$url.'" target="_blank" class="text-primary-600 font-medium underline">⬇ '.$name.'</a>'
                );
            } else {
                $content = $name.' (berkas tidak ditemukan)';
            }
        }

        return Placeholder::make('Berkas Jawaban')
            ->label('Berkas Jawaban')
            ->content($content);
    }

    private static function catatanField(?string $catatan): Placeholder
    {
        return Placeholder::make('Catatan')
            ->label('Catatan')
            ->content($catatan ? new HtmlString(nl2br(e($catatan))) : 'Tidak ada catatan');
    }
}
