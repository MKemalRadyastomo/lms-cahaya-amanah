<?php

namespace App\Filament\Resources\PengumpulanTugas\Pages;

use App\Filament\Resources\PengumpulanTugas\PengumpulanTugasResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPengumpulanTugas extends EditRecord
{
    protected static string $resource = PengumpulanTugasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * Saat menyimpan penilaian, tandai status 'dinilai' dan catat waktu.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['nilai']) && $data['nilai'] !== null && $data['nilai'] !== '') {
            $data['status'] = 'dinilai';
            $data['dinilai_at'] = now();
        }

        return $data;
    }
}
