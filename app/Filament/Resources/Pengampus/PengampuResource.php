<?php

namespace App\Filament\Resources\Pengampus;

use App\Filament\Resources\Pengampus\Pages\CreatePengampu;
use App\Filament\Resources\Pengampus\Pages\EditPengampu;
use App\Filament\Resources\Pengampus\Pages\ListPengampus;
use App\Filament\Resources\Pengampus\Schemas\PengampuForm;
use App\Filament\Resources\Pengampus\Tables\PengampusTable;
use App\Models\Pengampu;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PengampuResource extends Resource
{
    protected static ?string $model = Pengampu::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    public static function getNavigationGroup(): ?string
    {
        return 'Akademik';
    }

    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return 'Pengampu';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Pengampu (Guru per Mapel)';
    }

    public static function form(Schema $schema): Schema
    {
        return PengampuForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PengampusTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPengampus::route('/'),
            'create' => CreatePengampu::route('/create'),
            'edit' => EditPengampu::route('/{record}/edit'),
        ];
    }
}
