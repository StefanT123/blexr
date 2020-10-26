<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkFromHome extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'work_from_home';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['date', 'hours', 'approved'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'date',
    ];

    /**
     * Get work from home request date.
     *
     * @param  string  $value
     * @return string
     */
    public function getDateAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('d-m-Y');
    }

    /**
     * Work from home request is for one employee.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
