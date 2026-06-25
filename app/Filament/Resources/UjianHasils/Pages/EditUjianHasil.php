<?php

namespace App\Filament\Resources\UjianHasils\Pages;

use App\Filament\Resources\UjianHasils\UjianHasilResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUjianHasil extends EditRecord
{
    protected static string $resource = UjianHasilResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
