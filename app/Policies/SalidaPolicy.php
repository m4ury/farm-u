<?php

namespace App\Policies;

use App\Models\Salida;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SalidaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): response
    {
        return ($user->isAdmin() || $user->isUrgencias())
            ? Response::allow()
            : Response::deny('No tienes permiso para acceder a esta vista.');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Salida $salida): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): response
    {
        return ($user->isAdmin() || $user->isUrgencias())
            ? Response::allow()
            : Response::deny('No tienes permiso para crear salidas.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Salida $salida): response
    {
        return ($user->isAdmin() || $user->isUrgencias())
            ? Response::allow()
            : Response::deny('No tienes permiso para actualizar salidas.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Salida $salida): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Salida $salida): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Salida $salida): bool
    {
        //
    }
}
