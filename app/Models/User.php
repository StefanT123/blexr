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
     * User can make one request to work from home.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function workFromHomeRequest()
    {
        return $this->hasMany(WorkFromHome::class);
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
     * Check if the user can make work from home request.
     *
     * Request can be made if it's been requested
     * 4 hours before the end of the day.
     *
     * @return bool
     */
    public function canMakeWorkFromHomeRequest()
    {
        return now()->addHours(4) < now()->endOfDay();
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
