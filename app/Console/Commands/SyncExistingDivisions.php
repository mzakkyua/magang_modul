<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Division;
use App\Models\VacancyMagang;
use App\Models\DivisionSetting;
use App\Models\MagangAccessRight;

class SyncExistingDivisions extends Command
{
    protected $signature = 'divisions:sync-existing';

    protected $description = 'Sync seluruh division lama ke master divisions_magang';

    public function handle(): int
    {
        $this->info('Sync existing divisions...');

        $divisions = collect()

            // dari vacancies
            ->merge(
                VacancyMagang::query()
                    ->whereNotNull('division_name')
                    ->pluck('division_name')
            )

            // dari division settings
            ->merge(
                DivisionSetting::query()
                    ->whereNotNull('division_name')
                    ->pluck('division_name')
            )

            // dari access rights
            ->merge(
                MagangAccessRight::query()
                    ->whereNotNull('division_name')
                    ->pluck('division_name')
            )

            // normalize
            ->map(function ($name) {
                return ucwords(trim($name));
            })

            // remove empty
            ->filter()

            // unique
            ->unique()

            // sort
            ->sort()
            ->values();

        $created = 0;

        foreach ($divisions as $name) {

            $exists = Division::whereRaw(
                'LOWER(name) = ?',
                [strtolower($name)]
            )->exists();

            if (!$exists) {

                Division::create([
                    'name' => $name,
                    'is_active' => true,
                ]);

                $this->line("✔ {$name}");

                $created++;
            }
        }

        $this->newLine();

        $this->info("Selesai. {$created} divisi berhasil disinkronkan.");

        return self::SUCCESS;
    }
}
