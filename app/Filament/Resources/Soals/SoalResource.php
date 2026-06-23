<?php

namespace App\Filament\Resources\Soals;

use App\Filament\Resources\Soals\Pages\CreateSoal;
use App\Filament\Resources\Soals\Pages\EditSoal;
use App\Filament\Resources\Soals\Pages\ListSoals;
use App\Filament\Resources\Soals\Schemas\SoalForm;
use App\Filament\Resources\Soals\Tables\SoalsTable;
use App\Models\Soal;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SoalResource extends Resource
{
    protected static ?string $model = Soal::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    public static function getNavigationGroup(): ?string
    {
        return 'Ujian & Penilaian';
    }

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return 'Soal';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Bank Soal';
    }

    public static function form(Schema $schema): Schema
    {
        return SoalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SoalsTable::configure($table);
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
            'index' => ListSoals::route('/'),
            'create' => CreateSoal::route('/create'),
            'edit' => EditSoal::route('/{record}/edit'),
        ];
    }
}
