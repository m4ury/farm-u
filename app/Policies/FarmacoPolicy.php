<?php

namespace App\Policies;

use App\Models\Farmaco;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\AuthorizationException;

class FarmacoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return ($user->isAdmin() || $user->isFarmacia())
            ? Response::allow()
            : Response::deny('No tienes permiso para acceder a esta vista.');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Farmaco $farmaco): Response
    {
        return ($user->isAdmin() || $user->isFarmacia())
            ? Response::allow()
            : Response::deny('No tienes permiso para ver este fármaco.');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return ($user->isAdmin() || $user->isFarmacia())
            ? Response::allow()
            : Response::deny('No tienes permiso para crear fármacos.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Farmaco $farmaco): bool
    {
        return $user->isAdmin() || $user->isFarmacia();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Farmaco $farmaco): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Farmaco $farmaco): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Farmaco $farmaco): bool
    {
        //
    }
}
