<?php

namespace App\Utilities\Filters\WorkFromHomeFilters;

use App\Utilities\Filters\QueryFilter;
use App\Utilities\Filters\FilterContract;

class Status extends QueryFilter implements FilterContract
{
    public function handle($value): void
    {
        switch ($value) {
            case 'approved':
                $value = true;
                break;
            case 'denied':
                $value = false;
                break;
            default:
                $value = null;
                break;
        }

        $this->query->where('approved', $value);
    }
}
