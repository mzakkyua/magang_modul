<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VacancyMagang;

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
    public function update(User $user, VacancyMagang $vacancy): bool
    {
        $access = $user->magangAccessRight;

        if (!$access) {
            return false;
        }

        // Superadmin bebas edit semua
        if ($access->role === 'superadmin') {
            return true;
        }

        // Admin hanya boleh edit divisinya
        return $access->division_name === $vacancy->division_name;
    }
}
