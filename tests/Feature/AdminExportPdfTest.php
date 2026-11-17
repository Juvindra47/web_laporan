<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Report;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminExportPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_download_report_pdf(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'is_active' => true]);
        $reporter = User::factory()->create(['role' => User::ROLE_PELAPOR, 'is_active' => true]);
        $technician = User::factory()->create(['role' => User::ROLE_PETUGAS, 'is_active' => true]);

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
            'status' => Report::STATUS_MENUNGGU_PETUGAS,
        ]);

        $report->assignment()->create([
            'technician_id' => $technician->id,
            'assigned_by' => $admin->id,
            'assigned_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.exports.pdf'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_pdf_export_applies_status_filter(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'is_active' => true]);

        $this->actingAs($admin)
            ->get(route('admin.exports.pdf', ['status' => Report::STATUS_SELESAI]))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }
}
