<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\VacancyMagang;
use App\Models\MagangAccessRight;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VacancyMagangPolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_bidang_tidak_bisa_edit_lowongan_divisi_lain()
    {
        // =====================================================
        // 1. BUAT USER ADMIN BIDANG
        // =====================================================
        $admin = User::factory()->create();

        MagangAccessRight::create([
            'user_id' => $admin->id,
            'role' => 'admin_bidang', // SESUAI MIGRATION
            'division_name' => 'IT',
        ]);

        // =====================================================
        // 2. BUAT LOWONGAN DIVISI LAIN
        // =====================================================
        $vacancy = VacancyMagang::factory()->create([
            'division_name' => 'Keuangan',
        ]);

        // =====================================================
        // 3. LOGIN SEBAGAI ADMIN IT
        // =====================================================
        $this->actingAs($admin);

        // =====================================================
        // 4. AKSES HALAMAN EDIT
        // =====================================================
        $response = $this->get(
            route('admin.vacancies.edit', $vacancy)
        );

        // =====================================================
        // 5. PASTIKAN DITOLAK (403)
        // =====================================================
        $response->assertStatus(403);
    }
}
