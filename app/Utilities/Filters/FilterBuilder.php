<?php

namespace App\Utilities\Filters;

class FilterBuilder
{
    protected $query;
    protected $filters;
    protected $namespace;

    /**
     * Initialize this class.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array                                 $filters
     * @param string                                $namespace
     */
    public function __construct($query, array $filters, string $namespace)
    {
        $this->query = $query;
        $this->filters = $filters;
        $this->namespace = $namespace;
    }

    /**
     * Apply the given filters.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply()
    {
        foreach ($this->filters as $name => $value) {
            $normailizedName = ucfirst($name);
            $class = $this->namespace . "\\{$normailizedName}";

            if (! class_exists($class)) {
                continue;
            }

            if (strlen($value)) {
                (new $class($this->query))->handle($value);
            } else {
                (new $class($this->query))->handle();
            }
        }

        return $this->query;
    }
}
