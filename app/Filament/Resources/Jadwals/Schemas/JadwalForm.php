<?php

namespace App\Filament\Resources\Jadwals\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class JadwalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('kelas_id')
                    ->label('Kelas')
                    ->relationship('kelas', 'nama')
                    ->required()->searchable()->preload(),
                Select::make('mapel_id')
                    ->label('Mata Pelajaran')
                    ->relationship('mapel', 'nama')
                    ->required()->searchable()->preload(),
                Select::make('guru_id')
                    ->label('Guru')
                    ->relationship('guru', 'name', modifyQueryUsing: fn ($query) => $query->where('role', User::ROLE_GURU))
                    ->required()->searchable()->preload(),
                Select::make('hari')
                    ->required()
                    ->options([
                        'senin' => 'Senin',
                        'selasa' => 'Selasa',
                        'rabu' => 'Rabu',
                        'kamis' => 'Kamis',
                        'jumat' => 'Jumat',
                        'sabtu' => 'Sabtu',
                        'minggu' => 'Minggu',
                    ]),
                TextInput::make('jam_mulai')->label('Jam Mulai')->type('time')->required(),
                TextInput::make('jam_selesai')->label('Jam Selesai')->type('time')->required()->rule('after:jam_mulai'),
                TextInput::make('ruang')->maxLength(50)->placeholder('Contoh: R. 101 (opsional)'),
                Select::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->relationship('tahunAjaran', 'tahun')
                    ->required()->searchable()->preload(),
            ]);
    }
}
