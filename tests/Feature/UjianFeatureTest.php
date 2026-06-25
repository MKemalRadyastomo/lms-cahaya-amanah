<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\KelasSiswa;
use App\Models\Mapel;
use App\Models\Soal;
use App\Models\TahunAjaran;
use App\Models\Ujian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UjianFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function setupSiswaWithKelas(): array
    {
        $siswa = User::factory()->create(['role' => 'siswa', 'status' => 'aktif']);

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

        $guru = User::factory()->create(['role' => 'guru', 'status' => 'aktif']);

        $mapel = Mapel::create([
            'kode' => 'MTK',
            'nama' => 'Matematika',
            'jenjang' => 'SMA',
            'kkm' => 70,
        ]);

        return [$siswa, $kelas, $ta, $guru, $mapel];
    }

    private function createUjianWithSoal($kelas, $guru, $mapel, array $ujianOverrides = []): Ujian
    {
        $ujian = Ujian::create(array_merge([
            'mapel_id' => $mapel->id,
            'kelas_id' => $kelas->id,
            'guru_id' => $guru->id,
            'judul' => 'Ujian Bab 1',
            'deskripsi' => 'Ujian matematika',
            'jenis' => 'ujian',
            'durasi_menit' => 60,
            'waktu_mulai' => now()->subMinutes(5),
            'waktu_selesai' => now()->addHours(2),
            'is_published' => true,
        ], $ujianOverrides));

        Soal::create([
            'ujian_id' => $ujian->id,
            'tipe' => 'pg',
            'pertanyaan' => '2 + 2 = ?',
            'opsi' => ['3', '4', '5', '6'],
            'jawaban_benar' => '4',
            'poin' => 2,
        ]);

        Soal::create([
            'ujian_id' => $ujian->id,
            'tipe' => 'pg',
            'pertanyaan' => 'Ibu kota Indonesia?',
            'opsi' => ['Bandung', 'Jakarta', 'Surabaya', 'Medan'],
            'jawaban_benar' => 'Jakarta',
            'poin' => 2,
        ]);

        return $ujian;
    }

    public function test_siswa_can_view_ujian_list(): void
    {
        [$siswa, $kelas, $ta, $guru, $mapel] = $this->setupSiswaWithKelas();
        $ujian = $this->createUjianWithSoal($kelas, $guru, $mapel);

        $this->actingAs($siswa)
            ->get('/siswa/ujian')
            ->assertOk()
            ->assertSee($ujian->judul, false);
    }

    public function test_siswa_can_start_and_take_ujian(): void
    {
        [$siswa, $kelas, $ta, $guru, $mapel] = $this->setupSiswaWithKelas();
        $ujian = $this->createUjianWithSoal($kelas, $guru, $mapel);

        $this->actingAs($siswa);

        $this->get("/siswa/ujian/{$ujian->id}")->assertOk()->assertSee('Mulai Ujian', false);

        $this->post("/siswa/ujian/{$ujian->id}/mulai")
            ->assertRedirect("/siswa/ujian/{$ujian->id}/kerjakan");

        $this->get("/siswa/ujian/{$ujian->id}/kerjakan")->assertOk()->assertSee('2 + 2 = ?', false);

        $this->assertDatabaseHas('ujian_hasils', [
            'ujian_id' => $ujian->id,
            'siswa_id' => $siswa->id,
            'status' => 'sedang',
        ]);
    }

    public function test_ujian_requires_passcode(): void
    {
        [$siswa, $kelas, $ta, $guru, $mapel] = $this->setupSiswaWithKelas();
        $ujian = $this->createUjianWithSoal($kelas, $guru, $mapel, ['passcode' => 'RAHASIA']);

        $this->actingAs($siswa)
            ->post("/siswa/ujian/{$ujian->id}/mulai", ['passcode' => 'salah'])
            ->assertSessionHasErrors('passcode');

        $this->post("/siswa/ujian/{$ujian->id}/mulai", ['passcode' => 'RAHASIA'])
            ->assertRedirect("/siswa/ujian/{$ujian->id}/kerjakan");
    }

    public function test_autosave_persists_jawaban(): void
    {
        [$siswa, $kelas, $ta, $guru, $mapel] = $this->setupSiswaWithKelas();
        $ujian = $this->createUjianWithSoal($kelas, $guru, $mapel);

        $this->actingAs($siswa);
        $this->post("/siswa/ujian/{$ujian->id}/mulai");

        $soalIds = $ujian->soals()->pluck('id')->all();
        $jawaban = [$soalIds[0] => '4'];

        $this->postJson("/siswa/ujian/{$ujian->id}/simpan", ['jawaban' => $jawaban])
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertDatabaseHas('ujian_hasils', [
            'ujian_id' => $ujian->id,
            'siswa_id' => $siswa->id,
        ]);

        $record = \DB::table('ujian_hasils')->where('ujian_id', $ujian->id)->where('siswa_id', $siswa->id)->first();
        $this->assertSame('4', json_decode($record->jawaban, true)[$soalIds[0]]);
    }

    public function test_submit_auto_grades_pg_and_shows_hasil(): void
    {
        [$siswa, $kelas, $ta, $guru, $mapel] = $this->setupSiswaWithKelas();
        $ujian = $this->createUjianWithSoal($kelas, $guru, $mapel, ['tampilkan_hasil' => true]);

        $this->actingAs($siswa);
        $this->post("/siswa/ujian/{$ujian->id}/mulai");

        $soalIds = $ujian->soals()->pluck('id')->all();

        // Satu benar satu salah -> 50% (2 dari 4 poin)
        $this->post("/siswa/ujian/{$ujian->id}/submit", [
            'jawaban' => [$soalIds[0] => '4', $soalIds[1] => 'Bandung'],
        ])->assertRedirect("/siswa/ujian/{$ujian->id}/hasil");

        $this->assertDatabaseHas('ujian_hasils', [
            'ujian_id' => $ujian->id,
            'siswa_id' => $siswa->id,
            'status' => 'selesai',
            'nilai' => 50,
        ]);

        $this->get("/siswa/ujian/{$ujian->id}/hasil")
            ->assertOk()
            ->assertSee('50');
    }

    public function test_siswa_cannot_retake_finished_ujian(): void
    {
        [$siswa, $kelas, $ta, $guru, $mapel] = $this->setupSiswaWithKelas();
        $ujian = $this->createUjianWithSoal($kelas, $guru, $mapel);

        $this->actingAs($siswa);
        $this->post("/siswa/ujian/{$ujian->id}/mulai");

        $soalIds = $ujian->soals()->pluck('id')->all();
        $this->post("/siswa/ujian/{$ujian->id}/submit", [
            'jawaban' => [$soalIds[0] => '4', $soalIds[1] => 'Jakarta'],
        ]);

        // Mencoba mulai lagi harus redirect ke hasil
        $this->post("/siswa/ujian/{$ujian->id}/mulai")
            ->assertRedirect("/siswa/ujian/{$ujian->id}/hasil");
    }
}
