<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Materi;
use App\Models\Pengampu;
use App\Models\PengumpulanTugas;
use App\Models\Soal;
use App\Models\Tugas;
use App\Models\Ujian;
use App\Models\UjianHasil;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KontenSeeder extends Seeder
{
    /** Bank materi per kode mapel: [judul, konten] */
    private array $bankMateri = [
        'MTK' => [
            ['Sistem Persamaan Linear', 'Pembahasan SPLDV dengan metode substitusi dan eliminasi.'],
            ['Fungsi Kuadrat', 'Grafik dan titik puncak fungsi kuadrat ax²+bx+c.'],
        ],
        'ARB' => [
            ['Mufradat Harian', 'Kosakata sehari-hari untuk percakapan dasar.'],
            ['Qawaid Nahwu', 'Aturan dasar tata bahasa Arab.'],
        ],
        'FQH' => [
            ['Thaharah', 'Tata cara bersuci: wudhu, tayamum, dan mandi wajib.'],
            ['Shalat Berjamaah', 'Hukum dan tata cara shalat berjamaah.'],
        ],
        'QHD' => [
            ['Hadits Arbain 1', 'Hadits pertama: niat segala amal perbuatan.'],
            ['Pengantar Ilmu Hadits', 'Pengertian hadits, sunnah, dan khabar.'],
        ],
        'ING' => [
            ['Simple Present Tense', 'Rumus dan penggunaan simple present tense.'],
            ['Daily Conversation', 'Contoh percakapan bahasa Inggris sehari-hari.'],
        ],
        'INF' => [
            ['Pengantar Logika Pemrograman', 'Konsep algoritma, flowchart, dan pseudocode dasar.'],
            ['Pengenalan HTML', 'Struktur dasar dokumen HTML untuk membuat halaman web.'],
        ],
    ];

    /** Bank tugas per kode mapel: [judul, deskripsi] */
    private array $bankTugas = [
        'MTK' => ['Latihan SPLDV', 'Kerjakan soal nomor 1-10 pada modul halaman 23. Kumpulkan jawaban dalam bentuk PDF.'],
        'ARB' => ['Terjemahan Mufradat', 'Terjemahkan 15 kata kosakata harian ke dalam bahasa Indonesia.'],
        'FQH' => ['Tata Cara Wudhu', 'Tulis urutan lengkap gerakan wudhu disertai dalilnya.'],
        'QHD' => ['Hafalan Hadits Arbain 1', 'Hafalkan hadits pertama dan tulis maknanya.'],
        'ING' => ['Essay Simple Present', 'Buat 10 kalimat menggunakan simple present tense.'],
        'INF' => ['Buat Flowchart', 'Gambar flowchart algoritma menentukan bilangan ganjil/genap.'],
    ];

    /** Bank ujian + soal per kode mapel */
    private array $bankUjian = [
        'MTK' => [
            'Ulangan Harian - Sistem Persamaan Linear',
            'Kerjakan dengan jujur. Ujian otomatis tersimpan saat waktu habis.',
            [
                ['Penyelesaian SPLDV x + y = 5 dan x - y = 1 adalah...', ['x=3, y=2', 'x=2, y=3', 'x=4, y=1', 'x=1, y=4'], 'x=3, y=2'],
                ['Gradien garis y = 2x + 3 adalah...', ['1', '2', '3', '-2'], '2'],
                ['Akar-akar persamaan x² - 5x + 6 = 0 adalah...', ['1 dan 6', '2 dan 3', '-2 dan -3', '1 dan 5'], '2 dan 3'],
                ['Hasil dari 3x + 2 = 17 adalah x = ...', ['3', '4', '5', '6'], '5'],
                ['Bentuk sederhana dari 2(x+3) adalah...', ['2x+3', '2x+6', 'x+6', '2x+5'], '2x+6'],
            ],
        ],
        'ARB' => [
            'Ulangan Harian - Mufradat',
            'Pilih terjemahan yang tepat.',
            [
                ['كِتَابٌ bermakna...', ['Pena', 'Buku', 'Meja', 'Pintu'], 'Buku'],
                ['مَدْرَسَةٌ bermakna...', ['Rumah', 'Pasar', 'Sekolah', 'Masjid'], 'Sekolah'],
                ['Lawan kata كَبِيرٌ adalah...', ['صَغِيرٌ', 'طَوِيلٌ', 'جَدِيدٌ', 'سَرِيعٌ'], 'صَغِيرٌ'],
                ['وَلَدٌ bermakna...', ['Anak perempuan', 'Anak laki-laki', 'Ibu', 'Ayah'], 'Anak laki-laki'],
                ['مَاءٌ bermakna...', ['Api', 'Tanah', 'Air', 'Udara'], 'Air'],
            ],
        ],
        'FQH' => [
            'Ulangan Harian - Thaharah',
            'Pilih jawaban yang paling benar.',
            [
                ['Wudhu diwajibkan sebelum...', ['Makan', 'Shalat', 'Tidur', 'Bekerja'], 'Shalat'],
                ['Syarat wudhu salah satunya...', ['Berpuasa', 'Berwudhu', 'Air suci', 'Mengaji'], 'Air suci'],
                ['Tayamum pengganti wudhu menggunakan...', ['Debu', 'Batu', 'Air', 'Daun'], 'Debu'],
                ['Membasuh tangan dalam wudhu sebanyak...', ['1x', '2x', '3x', '4x'], '3x'],
                ['Hadats kecil dapat dihilangkan dengan...', ['Mandi', 'Wudhu', 'Tayamum', 'Tidur'], 'Wudhu'],
            ],
        ],
        'QHD' => [
            'Ulangan Harian - Hadits Arbain',
            'Pilih jawaban yang benar.',
            [
                ['Hadits Arbain ke-1 membahas tentang...', ['Shalat', 'Niat', 'Zakat', 'Puasa'], 'Niat'],
                ['Jumlah hadits dalam Arbain Nawawi adalah...', ['20', '30', '40', '50'], '40'],
                ['Penyusun kitab Arbain Nawawi adalah...', ['Imam Bukhari', 'Imam Muslim', 'Imam Nawawi', 'Imam Ghazali'], 'Imam Nawawi'],
                ['Sesungguhnya amal itu tergantung...', ['Harta', 'Niat', 'Waktu', 'Tempat'], 'Niat'],
                ['Hadits adalah segala...', ['Perkataan Nabi', 'Ayat Al-Qur\'an', 'Ijtihad sahabat', 'Fatwa ulama'], 'Perkataan Nabi'],
            ],
        ],
        'ING' => [
            'Ulangan Harian - Simple Present Tense',
            'Choose the correct answer.',
            [
                ['She ___ to school every day.', ['go', 'goes', 'going', 'gone'], 'goes'],
                ['They ___ football on Sunday.', ['plays', 'playing', 'play', 'played'], 'play'],
                ['The sun ___ in the east.', ['rise', 'rises', 'rising', 'rose'], 'rises'],
                ['I ___ a student.', ['am', 'is', 'are', 'be'], 'am'],
                ['He ___ his teeth every morning.', ['brush', 'brushes', 'brushing', 'brushed'], 'brushes'],
            ],
        ],
        'INF' => [
            'Ulangan Harian - Algoritma',
            'Pilih jawaban yang benar.',
            [
                ['Simbol untuk input/output pada flowchart adalah...', ['Persegi', 'Jajaran genjang', 'Belah ketupat', 'Lingkaran'], 'Jajaran genjang'],
                ['Simbol untuk pengambilan keputusan adalah...', ['Persegi panjang', 'Belah ketupat', 'Oval', 'Segitiga'], 'Belah ketupat'],
                ['Pseudocode adalah...', ['Kode program', 'Deskripsi algoritma mirip kode', 'Bahasa mesin', 'Diagram'], 'Deskripsi algoritma mirip kode'],
                ['Struktur dasar algoritma di bawah ini KECUALI...', ['Sequensi', 'Percabangan', 'Perulangan', 'Kompilasi'], 'Kompilasi'],
                ['Tag utama dokumen HTML adalah...', ['<body>', '<html>', '<head>', '<p>'], '<html>'],
            ],
        ],
    ];

    public function run(): void
    {
        $ta = \App\Models\TahunAjaran::where('is_active', true)->first();

        $pengampus = Pengampu::with(['guru', 'mapel', 'kelas'])
            ->where('tahun_ajaran_id', $ta->id)
            ->get();

        foreach ($pengampus as $pengampu) {
            $kelas = $pengampu->kelas;
            $mapel = $pengampu->mapel;
            $guru = $pengampu->guru;
            $kode = $mapel->kode;
            $siswaList = $kelas->siswa()->with('siswa')->get()->pluck('siswa');

            // --- Materi ---
            foreach ($this->bankMateri[$kode] ?? [] as [$judul, $konten]) {
                Materi::create([
                    'mapel_id' => $mapel->id,
                    'kelas_id' => $kelas->id,
                    'guru_id' => $guru->id,
                    'judul' => $judul,
                    'slug' => Str::slug($judul.'-'.$kode.'-'.$kelas->id.'-'.Str::random(4)),
                    'konten' => $konten,
                    'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'is_published' => true,
                ]);
            }

            // --- Tugas ---
            if (isset($this->bankTugas[$kode])) {
                [$judul, $deskripsi] = $this->bankTugas[$kode];
                $tugas = Tugas::create([
                    'mapel_id' => $mapel->id,
                    'kelas_id' => $kelas->id,
                    'guru_id' => $guru->id,
                    'judul' => $judul,
                    'deskripsi' => $deskripsi,
                    'deadline' => now()->addDays(7),
                    'poin_max' => 100,
                    'is_published' => true,
                ]);

                // ~60% siswa mengumpulkan, sebagian sudah dinilai
                foreach ($siswaList as $i => $siswa) {
                    if ($i % 5 === 4) {
                        continue; // sebagian belum mengumpulkan
                    }
                    $dinilai = ($i % 5 < 2);
                    PengumpulanTugas::create([
                        'tugas_id' => $tugas->id,
                        'siswa_id' => $siswa->id,
                        'file_path' => "tugas/{$tugas->id}/siswa-{$siswa->id}.pdf",
                        'status' => $dinilai ? 'dinilai' : 'terkirim',
                        'nilai' => $dinilai ? (string) (75 + ($i % 3) * 8) : null,
                        'feedback' => $dinilai ? 'Kerja bagus, perhatikan kembali soal nomor akhir.' : null,
                        'submitted_at' => now()->subDays(1),
                        'dinilai_at' => $dinilai ? now() : null,
                    ]);
                }
            }

            // --- Ujian + Soal + Hasil ---
            if (isset($this->bankUjian[$kode])) {
                [$judul, $deskripsi, $soalBank] = $this->bankUjian[$kode];

                // Pastikan ada satu ujian yang sudah publish & lampau agar bisa dilihat hasilnya
                $selesai = $kelas->id % 2 === 0; // bergantian per kelas
                $ujian = Ujian::create([
                    'mapel_id' => $mapel->id,
                    'kelas_id' => $kelas->id,
                    'guru_id' => $guru->id,
                    'judul' => $judul,
                    'deskripsi' => $deskripsi,
                    'jenis' => 'ujian',
                    'durasi_menit' => 45,
                    'waktu_mulai' => $selesai ? now()->subDays(2) : now()->addDays(3),
                    'waktu_selesai' => $selesai ? now()->subDays(2)->addHours(2) : now()->addDays(3)->addHours(2),
                    'acak_soal' => true,
                    'acak_opsi' => true,
                    'tampilkan_hasil' => $selesai,
                    'is_published' => true,
                ]);

                $soalIds = [];
                foreach ($soalBank as [$pertanyaan, $opsi, $jawaban]) {
                    $soal = Soal::create([
                        'ujian_id' => $ujian->id,
                        'tipe' => 'pg',
                        'pertanyaan' => $pertanyaan,
                        'opsi' => $opsi,
                        'jawaban_benar' => $jawaban,
                        'poin' => 20,
                    ]);
                    $soalIds[] = $soal->id;
                }

                // Hasil ujian (jika ujian sudah selesai, semua siswa sudah ngerjain)
                $targetStatus = $selesai ? 'selesai' : 'sedang';
                foreach ($siswaList as $i => $siswa) {
                    if (! $selesai && $i % 5 >= 3) {
                        continue; // belum semua mulai
                    }

                    // bangun jawaban acak untuk simulasi
                    $jawaban = [];
                    foreach ($soalBank as $j => [$pertanyaan, $opsi, $jawabanBenar]) {
                        $benar = ($i + $j) % 4 === 0 ? false : true;
                        $jawaban[(string) $soalIds[$j]] = $benar
                            ? $jawabanBenar
                            : $opsi[($j + 1) % count($opsi)];
                    }

                    $nilai = $selesai ? (string) (60 + ($i % 4) * 10) : null;

                    UjianHasil::create([
                        'ujian_id' => $ujian->id,
                        'siswa_id' => $siswa->id,
                        'jawaban' => $jawaban,
                        'nilai' => $nilai,
                        'status' => $selesai ? 'selesai' : ($i % 5 < 3 ? 'sedang' : 'belum_mulai'),
                        'waktu_mulai' => $selesai ? now()->subDays(2) : now()->subMinutes(10),
                        'waktu_selesai' => $selesai ? now()->subDays(2)->addMinutes(40) : null,
                    ]);
                }
            }
        }
    }
}
