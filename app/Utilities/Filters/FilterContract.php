<?php

namespace App\Utilities\Filters;

interface FilterContract
{
    public function handle($value): void;
}
