<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\MagangAccessRight;

class MagangRoleSeeder extends Seeder
{
    public function run(): void
    {

        // Gembok Keamanan Lapisan Kedua
        if (app()->isProduction()) {
            $this->command->warn('Peringatan: MagangRoleSeeder dihentikan! Tidak boleh mereset password admin di server production.');
            return; // Hentikan eksekusi kode di bawahnya
        }
        /**
         * =====================================================
         * SUPERADMIN
         * =====================================================
         */
        $superAdmin = User::updateOrCreate(
            ['email' => 'bos@dinas.go.id'],
            [
                'name' => 'Bapak Kepala Dinas',
                'password' => Hash::make('password'),
            ]
        );

        MagangAccessRight::updateOrCreate(
            ['user_id' => $superAdmin->id],
            [
                'role' => 'superadmin',
                'division_name' => null,
            ]
        );

        /**
         * =====================================================
         * DUMMY ADMIN BIDANG
         * =====================================================
         *
         * Hanya untuk testing development.
         * Nantinya admin bidang akan dibuat
         * melalui panel superadmin.
         * =====================================================
         */

        $adminSekretariat = User::updateOrCreate(
            [
                'email' => 'sekretariat@dinas.go.id'
            ],
            [
                'name' => 'Admin Sekretariat',
                'password' => Hash::make('password'),
            ]
        );

        MagangAccessRight::updateOrCreate(
            [
                'user_id' => $adminSekretariat->id
            ],
            [
                'role' => 'admin_bidang',
                'division_name' => 'Sekretariat',
            ]
        );

        $adminPelatihan = User::updateOrCreate(
            [
                'email' => 'pelatihan@dinas.go.id'
            ],
            [
                'name' => 'Admin Pelatihan',
                'password' => Hash::make('password'),
            ]
        );

        MagangAccessRight::updateOrCreate(
            [
                'user_id' => $adminPelatihan->id
            ],
            [
                'role' => 'admin_bidang',
                'division_name' => 'Bidang Pelatihan dan Produktivitas',
            ]
        );

        $adminPengawasan = User::updateOrCreate(
            [
                'email' => 'pengawasan@dinas.go.id'
            ],
            [
                'name' => 'Admin Pengawasan',
                'password' => Hash::make('password'),
            ]
        );

        MagangAccessRight::updateOrCreate(
            [
                'user_id' => $adminPengawasan->id
            ],
            [
                'role' => 'admin_bidang',
                'division_name' => 'Bidang Pengawasan Ketenagakerjaan',
            ]
        );

        $adminPenempatan = User::updateOrCreate(
            [
                'email' => 'penempatan@dinas.go.id'
            ],
            [
                'name' => 'Admin Penempatan',
                'password' => Hash::make('password'),
            ]
        );

        MagangAccessRight::updateOrCreate(
            [
                'user_id' => $adminPenempatan->id
            ],
            [
                'role' => 'admin_bidang',
                'division_name' => 'Bidang Penempatan Perluasan Kesempatan Kerja',
            ]
        );

        $adminHI = User::updateOrCreate(
            [
                'email' => 'hi@dinas.go.id'
            ],
            [
                'name' => 'Admin Hubungan Industrial',
                'password' => Hash::make('password'),
            ]
        );

        MagangAccessRight::updateOrCreate(
            [
                'user_id' => $adminHI->id
            ],
            [
                'role' => 'admin_bidang',
                'division_name' => 'Bidang Hubungan Industrial',
            ]
        );
    }
}
