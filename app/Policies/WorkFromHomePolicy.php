<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkFromHomePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\WorkFromHome  $workFromHome
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->check()->isAdmin();
    }
}
