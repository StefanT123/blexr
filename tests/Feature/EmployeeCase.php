<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;

class EmployeeCase extends TestCase
{
    use WithFaker;

    protected function createEmployee()
    {
        return User::factory()->create([
            'role_id' => Role::factory()->create(['name' => 'user']),
        ]);
    }
}
