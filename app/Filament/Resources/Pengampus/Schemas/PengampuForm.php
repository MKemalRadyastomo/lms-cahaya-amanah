<?php

namespace App\Filament\Resources\Pengampus\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class PengampuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('guru_id')
                    ->label('Guru')
                    ->relationship('guru', 'name', modifyQueryUsing: fn ($query) => $query->where('role', User::ROLE_GURU))
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('mapel_id')
                    ->label('Mata Pelajaran')
                    ->relationship('mapel', 'nama')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('kelas_id')
                    ->label('Kelas')
                    ->relationship('kelas', 'nama')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->relationship('tahunAjaran', 'tahun')
                    ->required()
                    ->searchable()
                    ->preload(),
            ]);
    }
}
