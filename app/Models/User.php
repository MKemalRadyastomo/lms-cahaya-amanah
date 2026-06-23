<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name', 'email', 'password', 'role', 'nis', 'nip', 'nisn',
    'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'alamat',
    'no_hp', 'foto', 'status',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    public const ROLE_ADMIN = 'admin';

    public const ROLE_GURU = 'guru';

    public const ROLE_SISWA = 'siswa';

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Hanya admin & guru yang boleh mengakses panel Filament.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_GURU], true);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isGuru(): bool
    {
        return $this->role === self::ROLE_GURU;
    }

    public function isSiswa(): bool
    {
        return $this->role === self::ROLE_SISWA;
    }

    /**
     * Redirect tujuan setelah login berdasarkan peran.
     */
    public function getHomeRoute(): string
    {
        return match ($this->role) {
            self::ROLE_SISWA => route('siswa.dashboard'),
            self::ROLE_GURU, self::ROLE_ADMIN => '/admin',
            default => route('dashboard'),
        };
    }

    /**
     * Pendaftaran kelas siswa pada tahun ajaran aktif (atau yang diberikan).
     */
    public function getActiveKelasSiswa(?int $tahunAjaranId = null): ?KelasSiswa
    {
        $query = $this->kelasSiswa()->with(['kelas', 'tahunAjaran']);

        if ($tahunAjaranId !== null) {
            $query->where('tahun_ajaran_id', $tahunAjaranId);
        } else {
            $activeTa = TahunAjaran::where('is_active', true)->first();
            if ($activeTa) {
                $query->where('tahun_ajaran_id', $activeTa->id);
            }
        }

        return $query->first();
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'tanggal_lahir' => 'date',
            'last_login_at' => 'datetime',
        ];
    }

    // --- Relasi sebagai Siswa ---

    public function kelasSiswa(): HasMany
    {
        return $this->hasMany(KelasSiswa::class, 'siswa_id');
    }

    public function pengumpulanTugas(): HasMany
    {
        return $this->hasMany(PengumpulanTugas::class, 'siswa_id');
    }

    public function ujianHasils(): HasMany
    {
        return $this->hasMany(UjianHasil::class, 'siswa_id');
    }

    public function nilais(): HasMany
    {
        return $this->hasMany(Nilai::class, 'siswa_id');
    }

    // --- Relasi sebagai Guru ---

    public function walikelasKelas(): HasMany
    {
        return $this->hasMany(Kelas::class, 'walikelas_id');
    }

    public function pengampus(): HasMany
    {
        return $this->hasMany(Pengampu::class, 'guru_id');
    }

    public function materis(): HasMany
    {
        return $this->hasMany(Materi::class, 'guru_id');
    }

    public function tugas(): HasMany
    {
        return $this->hasMany(Tugas::class, 'guru_id');
    }

    public function ujians(): HasMany
    {
        return $this->hasMany(Ujian::class, 'guru_id');
    }

    // --- Relasi umum ---

    public function notifikasis(): HasMany
    {
        return $this->hasMany(Notifikasi::class, 'user_id');
    }

    public function pengumuman(): HasMany
    {
        return $this->hasMany(Pengumuman::class, 'created_by');
    }
}
