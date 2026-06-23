<?php

namespace App\Filament\Resources\PengumpulanTugas;

use App\Filament\Resources\PengumpulanTugas\Pages\EditPengumpulanTugas;
use App\Filament\Resources\PengumpulanTugas\Pages\ListPengumpulanTugas;
use App\Filament\Resources\PengumpulanTugas\Schemas\PengumpulanTugasForm;
use App\Filament\Resources\PengumpulanTugas\Tables\PengumpulanTugasTable;
use App\Models\PengumpulanTugas;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PengumpulanTugasResource extends Resource
{
    protected static ?string $model = PengumpulanTugas::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxArrowDown;

    public static function getNavigationGroup(): ?string
    {
        return 'Pembelajaran';
    }

    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return 'Pengumpulan Tugas';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Pengumpulan Tugas';
    }

    /**
     * Guru tidak dapat membuat pengumpulan manual (dibuat otomatis oleh siswa).
     */
    public static function canCreate(): bool
    {
        return false;
    }

    /**
     * Batasi query: guru hanya melihat pengumpulan dari tugas miliknya.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = auth()->user();

        if ($user && $user->isGuru()) {
            $query->whereHas('tugas', fn (Builder $q) => $q->where('guru_id', $user->id));
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return PengumpulanTugasForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PengumpulanTugasTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPengumpulanTugas::route('/'),
            'edit' => EditPengumpulanTugas::route('/{record}/edit'),
        ];
    }
}
