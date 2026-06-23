<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Akun')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation) => $operation === 'create')
                            ->helperText('Kosongkan jika tidak ingin mengubah password.'),
                        Select::make('role')
                            ->label('Peran')
                            ->required()
                            ->options([
                                'admin' => 'Admin',
                                'guru' => 'Guru',
                                'siswa' => 'Siswa',
                            ]),
                        ToggleButtons::make('status')
                            ->inline()
                            ->required()
                            ->default('aktif')
                            ->options([
                                'aktif' => 'Aktif',
                                'nonaktif' => 'Nonaktif',
                            ])
                            ->colors([
                                'aktif' => 'success',
                                'nonaktif' => 'danger',
                            ]),
                    ])
                    ->columns(2),

                Section::make('Data Pribadi')
                    ->schema([
                        ToggleButtons::make('jenis_kelamin')
                            ->inline()
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ])
                            ->colors([
                                'L' => 'info',
                                'P' => 'pink',
                            ]),
                        TextInput::make('nis')->label('NIS')->maxLength(50),
                        TextInput::make('nip')->label('NIP')->maxLength(50),
                        TextInput::make('nisn')->label('NISN')->maxLength(10),
                        TextInput::make('tempat_lahir')->label('Tempat Lahir')->maxLength(100),
                        DateTimePicker::make('tanggal_lahir')->label('Tanggal Lahir'),
                        TextInput::make('no_hp')->label('No. HP')->tel()->maxLength(20),
                        Textarea::make('alamat')->columnSpanFull(),
                        FileUpload::make('foto')->image()->directory('foto-user')->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
