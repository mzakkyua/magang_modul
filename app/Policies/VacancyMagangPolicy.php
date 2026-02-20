<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VacancyMagang;
use App\Models\MagangAccessRight;

class VacancyMagangPolicy
{
    /**
     * =====================================================
     * RULE: update
     * =====================================================
     * Siapa yang boleh edit lowongan?
     * - Superadmin → semua
     * - Admin bidang → hanya divisinya
     * =====================================================
     */
    public function update(User $user, VacancyMagang $vacancy)
    {
        $hakAkses = MagangAccessRight::where('user_id', $user->id)->first();

        if (!$hakAkses) {
            return false;
        }

        // Superadmin bebas
        if ($hakAkses->role === 'superadmin') {
            return true;
        }

        // Admin bidang hanya divisinya
        return $hakAkses->division_name === $vacancy->division_name;
    }
}
