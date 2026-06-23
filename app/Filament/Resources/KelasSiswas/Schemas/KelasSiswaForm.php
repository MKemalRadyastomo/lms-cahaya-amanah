<?php

namespace App\Filament\Resources\KelasSiswas\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class KelasSiswaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('siswa_id')
                    ->label('Siswa')
                    ->relationship('siswa', 'name', modifyQueryUsing: fn ($query) => $query->where('role', User::ROLE_SISWA))
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
