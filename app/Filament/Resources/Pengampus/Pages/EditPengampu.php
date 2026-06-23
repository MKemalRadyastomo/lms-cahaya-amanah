<?php

namespace App\Filament\Resources\Pengampus\Pages;

use App\Filament\Resources\Pengampus\PengampuResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPengampu extends EditRecord
{
    protected static string $resource = PengampuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
