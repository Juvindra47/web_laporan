<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Report;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_shows_latest_reports_with_their_status(): void
    {
        $reporter = User::factory()->create([
            'name' => 'Budi Santoso',
            'role' => User::ROLE_PELAPOR,
            'is_active' => true,
        ]);
        $category = Category::create(['name' => 'Listrik']);
        $room = Room::create(['room_name' => 'Lab Komputer', 'building' => 'Gedung A']);

        $report = Report::create([
            'ticket_number' => Report::generateTicketNumber(),
            'user_id' => $reporter->id,
            'category_id' => $category->id,
            'room_id' => $room->id,
            'title' => 'Lampu kelas mati',
            'description' => 'Lampu di ruang kelas tidak menyala.',
            'photo' => 'reports/contoh.jpg',
            'status' => Report::STATUS_MENUNGGU_VERIFIKASI,
        ]);

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Laporan kerusakan terbaru')
            ->assertSee($report->ticket_number)
            ->assertSee('Lampu kelas mati')
            ->assertSee('Menunggu Verifikasi')
            ->assertSee('Budi Santoso')
            ->assertSee('Lab Komputer (Gedung A)')
            ->assertSee('data-status="'.Report::STATUS_MENUNGGU_VERIFIKASI.'"', false);
    }

    public function test_landing_page_does_not_show_rejected_reports(): void
    {
        $reporter = User::factory()->create([
            'role' => User::ROLE_PELAPOR,
            'is_active' => true,
        ]);
        $category = Category::create(['name' => 'Listrik']);
        $room = Room::create(['room_name' => 'Ruang Guru']);

        Report::create([
            'ticket_number' => Report::generateTicketNumber(),
            'user_id' => $reporter->id,
            'category_id' => $category->id,
            'room_id' => $room->id,
            'title' => 'Laporan yang ditolak admin',
            'description' => 'Laporan ini sudah ditolak.',
            'photo' => 'reports/ditolak.jpg',
            'status' => Report::STATUS_DITOLAK,
            'rejection_reason' => 'Bukan kerusakan sarana.',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Laporan yang ditolak admin');
    }
}
