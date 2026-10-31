<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, User $model): bool
    {
        return $user->isAdmin() || $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, User $model): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Admin Masjid tidak bisa mengubah akun Super Admin atau Admin Masjid lain
        if (in_array($model->role, ['super_admin', 'admin_masjid']) && $user->id !== $model->id) {
            return false;
        }

        return $user->isAdmin();
    }

    /**
     * User rule: Admin masjid tidak bisa menambah atau menghapus admin.
     */
    public function delete(User $user, User $model): bool
    {
        if ($user->isSuperAdmin()) {
            return $user->id !== $model->id; // Tidak bisa hapus diri sendiri
        }

        // Admin Masjid TIDAK BISA menghapus admin_masjid atau super_admin
        if (in_array($model->role, ['admin_masjid', 'super_admin'])) {
            return false;
        }

        return $user->isAdmin();
    }
}
