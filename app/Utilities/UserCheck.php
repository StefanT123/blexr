<?php

namespace App\Utilities;

use App\Models\User;

class UserCheck
{
    /**
     * Instance of user.
     *
     * @var \App\User
     */
    protected $user;

    /**
     * Create new UserCheck instance.
     *
     * @param \App\User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Checks if the user is admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->user->user_role === 'admin';
    }
}
