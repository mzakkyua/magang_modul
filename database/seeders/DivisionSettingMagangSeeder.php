<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DivisionSettingMagangSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * =====================================================
         * DIVISI RESMI DISNAKERTRANS
         * =====================================================
         */

        $divisions = [

            [
                'division_name'      => 'Sekretariat',
                'max_open_vacancies' => 6,
            ],

            [
                'division_name'      => 'Bidang Pelatihan dan Produktivitas',
                'max_open_vacancies' => 6,
            ],

            [
                'division_name'      => 'Bidang Pengawasan Ketenagakerjaan',
                'max_open_vacancies' => 6,
            ],

            [
                'division_name'      => 'Bidang Penempatan Perluasan Kesempatan Kerja',
                'max_open_vacancies' => 6,
            ],

            [
                'division_name'      => 'Bidang Hubungan Industrial',
                'max_open_vacancies' => 6,
            ],
        ];

        foreach ($divisions as $division) {

            /**
             * --------------------------------------------------
             * 1. MASTER DIVISI
             * --------------------------------------------------
             *
             * Mengisi divisions_magang sebagai source of truth
             * untuk seluruh dropdown divisi di sistem.
             *
             * Tanpa ini, dropdown di form buat lowongan kosong
             * setelah migrate:fresh --seed.
             */
            Division::firstOrCreate(
                ['name'      => $division['division_name']],
                ['is_active' => true]
            );

            /**
             * --------------------------------------------------
             * 2. SETTING KAPASITAS
             * --------------------------------------------------
             *
             * Mengisi division_settings_magang untuk konfigurasi
             * kuota slot lowongan per divisi.
             */
            DB::table('division_settings_magang')->updateOrInsert(
                [
                    'division_name' => $division['division_name'],
                ],
                [
                    'max_open_vacancies' => $division['max_open_vacancies'],
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]
            );
        }
    }
}
