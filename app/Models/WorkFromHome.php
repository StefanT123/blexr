<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Utilities\Filters\FilterBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    /**
     * Scope a query to filter records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  array                                 $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterBy($query, array $filters)
    {
        $namespace = 'App\Utilities\Filters\WorkFromHomeFilters';
        $filter = new FilterBuilder($query, $filters, $namespace);

        return $filter->apply();
    }
}
