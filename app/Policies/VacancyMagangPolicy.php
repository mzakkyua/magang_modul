<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VacancyMagang;
use App\Models\MagangAccessRight;

class VacancyMagangPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->magangAccessRight !== null;
    }

    public function create(User $user): bool
    {
        return $user->magangAccessRight !== null;
    }

    public function update(User $user, VacancyMagang $vacancy): bool
    {
        $access = $user->magangAccessRight;

        if (!$access) {
            return false;
        }

        if ($access->role === MagangAccessRight::ROLE_SUPERADMIN) {
            return true;
        }

        return $access->division_name === $vacancy->division_name;
    }

    public function delete(User $user, VacancyMagang $vacancy): bool
    {
        return $this->update($user, $vacancy);
    }

    public function archive(User $user, VacancyMagang $vacancy): bool
    {
        // Hak arsip sama dengan hak update — superadmin semua divisi, admin divisi hanya divisinya
        return $this->update($user, $vacancy);
    }
}
