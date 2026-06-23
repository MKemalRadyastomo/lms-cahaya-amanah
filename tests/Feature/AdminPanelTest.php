<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'status' => 'aktif',
        ]);
    }

    public static function panelRoutesProvider(): array
    {
        $resources = [
            'users', 'tahun-ajarans', 'kelas', 'mapels', 'pengampus',
            'kelas-siswas', 'jadwals', 'materis', 'tugas', 'ujians',
            'soals', 'nilais', 'pengumumen',
        ];

        $routes = ['/admin'];
        foreach ($resources as $r) {
            $routes[] = "/admin/{$r}";
            $routes[] = "/admin/{$r}/create";
        }

        return array_combine($routes, array_map(fn ($r) => [$r], $routes));
    }

    #[DataProvider('panelRoutesProvider')]
    public function test_admin_can_view_panel_index_page(string $route): void
    {
        $this->actingAs($this->admin());

        $this->get($route)->assertOk();
    }

    public function test_guru_is_blocked_from_admin_only_resources(): void
    {
        $guru = User::factory()->create([
            'role' => 'guru',
            'status' => 'aktif',
        ]);

        $this->actingAs($guru);

        // Admin-only resources: forbidden for guru (no role escalation)
        $this->get('/admin/users')->assertForbidden();
        $this->get('/admin/tahun-ajarans')->assertForbidden();

        // Guru still has access to teaching-related resources
        $this->get('/admin/ujians')->assertOk();
        $this->get('/admin/materis')->assertOk();
    }
}
