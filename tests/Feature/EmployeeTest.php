<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\License;
use Laravel\Passport\Passport;

class EmployeeTest extends EmployeeCase
{
    /** @test */
    public function employee_cant_see_data_of_another_employee()
    {
        $employees = User::factory()->count(2)->create([
            'role_id' => Role::factory(),
        ]);

        $john = $employees[0];
        $jane = $employees[1];

        Passport::actingAs($john);

        $resp = $this->get(route('employee.show', $jane));
        $resp->assertForbidden();
    }

    /** @test */
    public function admin_can_see_data_of_any_employee()
    {
        $admin = User::factory()->create([
            'role_id' => Role::factory()->create(['name' => 'admin']),
        ]);

        $employee = $this->createEmployee();

        Passport::actingAs($admin);

        $resp = $this->get(route('employee.show', $employee));
        $resp->assertOk();
    }

    /** @test */
    public function employee_can_see_his_data()
    {
        $employee = $this->createEmployee();

        Passport::actingAs($employee);

        $resp = $this->get(route('employee.show', $employee));
        $resp->assertOk();

        $resp->assertJsonFragment([
            'email' => $employee->email,
            'name' => $employee->name,
        ]);
    }

    /** @test */
    public function employee_can_see_his_licenses()
    {
        $employee = User::factory()
            ->hasAttached(
                License::factory()->count(3),
                ['completed' => $this->faker->boolean],
            )
            ->create([
                'role_id' => Role::factory()->create(['name' => 'user']),
            ]);

        Passport::actingAs($employee);

        $resp = $this->get(route('license.show'));

        $resp->assertOk();

        $randomLicenseName = $employee->licenses()
            ->inRandomOrder()
            ->first()
            ->name;

        $resp->assertJsonFragment([
            'name' => $randomLicenseName,
        ]);
    }
}
