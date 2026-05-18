<?php

namespace Database\Seeders;

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
                'division_name' => 'Sekretariat',
                'max_open_vacancies' => 6,
            ],

            [
                'division_name' => 'Bidang Pelatihan dan Produktivitas',
                'max_open_vacancies' => 6,
            ],

            [
                'division_name' => 'Bidang Pengawasan Ketenagakerjaan',
                'max_open_vacancies' => 6,
            ],

            [
                'division_name' => 'Bidang Penempatan Perluasan Kesempatan Kerja',
                'max_open_vacancies' => 6,
            ],

            [
                'division_name' => 'Bidang Hubungan Industrial',
                'max_open_vacancies' => 6,
            ],
        ];

        foreach ($divisions as $division) {

            DB::table('division_settings_magang')->updateOrInsert(
                [
                    'division_name' => $division['division_name']
                ],
                [
                    'max_open_vacancies' => $division['max_open_vacancies'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
