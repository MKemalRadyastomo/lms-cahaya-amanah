<?php

namespace App\Filament\Resources\Ujians;

use App\Filament\Resources\Ujians\Pages\CreateUjian;
use App\Filament\Resources\Ujians\Pages\EditUjian;
use App\Filament\Resources\Ujians\Pages\ListUjians;
use App\Filament\Resources\Ujians\Schemas\UjianForm;
use App\Filament\Resources\Ujians\Tables\UjiansTable;
use App\Models\Ujian;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UjianResource extends Resource
{
    protected static ?string $model = Ujian::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboard;

    public static function getNavigationGroup(): ?string
    {
        return 'Ujian & Penilaian';
    }

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return 'Ujian';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Ujian & Kuis';
    }

    public static function form(Schema $schema): Schema
    {
        return UjianForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UjiansTable::configure($table);
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
            'index' => ListUjians::route('/'),
            'create' => CreateUjian::route('/create'),
            'edit' => EditUjian::route('/{record}/edit'),
        ];
    }
}
