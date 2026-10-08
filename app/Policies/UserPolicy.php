<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, User $target): bool
    {
        return $user->isAdmin();
    }

    /** Activar/desactivar: solo admin y nunca sobre su propia cuenta. */
    public function toggle(User $user, User $target): bool
    {
        return $user->isAdmin() && $user->id !== $target->id;
    }
}