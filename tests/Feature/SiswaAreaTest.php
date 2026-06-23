<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\KelasSiswa;
use App\Models\Mapel;
use App\Models\TahunAjaran;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SiswaAreaTest extends TestCase
{
    use RefreshDatabase;

    private function makeSiswa(): User
    {
        return User::factory()->create([
            'role' => 'siswa',
            'status' => 'aktif',
        ]);
    }

    private function makeSiswaWithKelas(): array
    {
        $siswa = $this->makeSiswa();

        $ta = TahunAjaran::create([
            'tahun' => '2024/2025',
            'semester' => 'ganjil',
            'is_active' => true,
        ]);

        $kelas = Kelas::create([
            'nama' => 'X IPA 1',
            'tingkat' => 'X',
            'jurusan' => 'IPA',
            'tahun_ajaran_id' => $ta->id,
        ]);

        KelasSiswa::create([
            'kelas_id' => $kelas->id,
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $ta->id,
        ]);

        return [$siswa, $kelas, $ta];
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/siswa')->assertRedirect('/login');
    }

    public function test_siswa_can_access_dashboard(): void
    {
        [$siswa] = $this->makeSiswaWithKelas();

        $this->actingAs($siswa)
            ->get('/siswa')
            ->assertOk()
            ->assertSee('Dashboard', false);
    }

    public function test_siswa_without_kelas_sees_empty_state(): void
    {
        $siswa = $this->makeSiswa();

        $this->actingAs($siswa)
            ->get('/siswa')
            ->assertOk()
            ->assertSee('Belum Terdaftar di Kelas', false);
    }

    public function test_guru_is_blocked_from_siswa_area(): void
    {
        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);

        $this->actingAs($guru)->get('/siswa')->assertForbidden();
    }

    public function test_siswa_can_view_materi_tugas_jadwal_pengumuman_nilai(): void
    {
        [$siswa] = $this->makeSiswaWithKelas();

        $this->actingAs($siswa);

        $this->get('/siswa/materi')->assertOk();
        $this->get('/siswa/tugas')->assertOk();
        $this->get('/siswa/jadwal')->assertOk();
        $this->get('/siswa/pengumuman')->assertOk();
        $this->get('/siswa/nilai')->assertOk();
    }

    public function test_siswa_can_submit_tugas_with_file(): void
    {
        [$siswa, $kelas, $ta] = $this->makeSiswaWithKelas();

        $mapel = Mapel::create([
            'kode' => 'MTK',
            'nama' => 'Matematika',
            'jenjang' => 'SMA',
            'kkm' => 70,
        ]);

        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);

        $tugas = Tugas::create([
            'mapel_id' => $mapel->id,
            'kelas_id' => $kelas->id,
            'guru_id' => $guru->id,
            'judul' => 'Tugas Bab 1',
            'deskripsi' => 'Kerjakan soal halaman 10',
            'deadline' => now()->addDays(3),
            'poin_max' => 100,
            'is_published' => true,
        ]);

        $this->actingAs($siswa);

        $this->get("/siswa/tugas/{$tugas->id}")->assertOk();

        $file = UploadedFile::fake()->create('jawaban.pdf', 100, 'application/pdf');

        $this->post("/siswa/tugas/{$tugas->id}/submit", [
            'file' => $file,
            'catatan' => 'Ini jawaban saya',
        ])->assertRedirect("/siswa/tugas/{$tugas->id}");

        $this->assertDatabaseHas('pengumpulan_tugas', [
            'tugas_id' => $tugas->id,
            'siswa_id' => $siswa->id,
            'status' => 'terkirim',
        ]);
    }

    public function test_dashboard_route_redirects_by_role(): void
    {
        $siswa = $this->makeSiswa();

        $this->actingAs($siswa)
            ->get('/dashboard')
            ->assertRedirect('/siswa');
    }
}
