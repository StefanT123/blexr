<?php

namespace App\Utilities;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

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

    /**
     * Checks if the user owns the passed model.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return bool
     */
    public function owns(Model $model)
    {
        return Auth::user()->is($model->owner);
    }
}
