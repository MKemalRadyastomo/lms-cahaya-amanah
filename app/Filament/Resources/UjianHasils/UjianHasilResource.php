<?php

namespace App\Filament\Resources\UjianHasils;

use App\Filament\Resources\UjianHasils\Pages\EditUjianHasil;
use App\Filament\Resources\UjianHasils\Pages\ListUjianHasils;
use App\Filament\Resources\UjianHasils\Schemas\UjianHasilForm;
use App\Filament\Resources\UjianHasils\Tables\UjianHasilsTable;
use App\Models\UjianHasil;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UjianHasilResource extends Resource
{
    protected static ?string $model = UjianHasil::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    public static function getNavigationGroup(): ?string
    {
        return 'Ujian & Penilaian';
    }

    protected static ?int $navigationSort = 4;

    public static function getModelLabel(): string
    {
        return 'Hasil Ujian';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Hasil Ujian Siswa';
    }

    public static function form(Schema $schema): Schema
    {
        return UjianHasilForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UjianHasilsTable::configure($table);
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
            'index' => ListUjianHasils::route('/'),
            'edit' => EditUjianHasil::route('/{record}/edit'),
        ];
    }
}
