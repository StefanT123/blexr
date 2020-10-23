<?php

namespace App\Models;

use App\Utilities\UserCheck;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * User can have only one role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * User can have many licenses.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function licenses()
    {
        return $this->belongsToMany(License::class)
            ->withPivot('completed');
    }

    /**
     * Get user's role.
     *
     * @return string
     */
    public function getUserRoleAttribute()
    {
        return $this->role->name;
    }

    /**
     * Make UserCheck instance.
     *
     * @return \App\Utilities\UserCheck
     */
    public function check()
    {
        return new UserCheck($this);
    }
}
