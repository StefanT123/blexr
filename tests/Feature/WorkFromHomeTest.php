<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\WorkFromHome;
use Laravel\Passport\Passport;

class WorkFromHomeTest extends EmployeeCase
{
    /** @test */
    public function not_logged_employee_cant_request_to_work_from_home()
    {
        $employee = $this->createEmployee();

        $resp = $this->json('post', route('employee.workFromHome'));

        $resp->assertUnauthorized();
    }

    /** @test */
    public function employee_can_request_to_work_from_home_on_specific_date()
    {
        $employee = $this->createEmployee();

        Passport::actingAs($employee);

        $date = $this->faker->dateTimeBetween('now', '+2 months')->format('d-m-Y');

        $resp = $this->json('post', route('employee.workFromHome'), [
            'date' => $date,
            'hours' => 5,
        ]);

        $resp->assertSuccessful();
        $this->assertDatabaseHas(
            'work_from_home',
            [
                'date' => $date,
                'hours' => 5,
                'user_id' => $employee->id,
            ],
        );
    }

    /** @test */
    public function employee_can_have_multiple_requests_to_work_from_home()
    {
        $employee = User::factory()
            ->has(
                WorkFromHome::factory()->count(3),
                'workFromHomeRequest'
            )
            ->create([
                'role_id' => Role::factory()->create(['name' => 'user']),
            ]);

        $this->assertCount(3, $employee->workFromHomeRequest);
    }

    /** @test */
    public function request_to_work_from_home_is_nullable_by_default()
    {
        $employee = $this->createEmployee();

        Passport::actingAs($employee);

        $date = $this->faker->dateTimeBetween('now', '+2 months')->format('d-m-Y');

        $resp = $this->json('post', route('employee.workFromHome'), [
            'date' => $date,
            'hours' => 5,
        ]);

        $workFromHomeRequestApproved = $employee->workFromHomeRequest->first()->approved;
        $this->assertEquals(null, $workFromHomeRequestApproved);
    }

    /** @test */
    public function work_from_home_request_must_be_made_4_hours_before_the_end_of_the_day()
    {
        // request is being made at 22:00
        $this->travelTo(now()->endOfDay()->subHours(2));

        $employee = $this->createEmployee();

        Passport::actingAs($employee);

        $date = $this->faker->dateTimeBetween('now', '+2 months')->format('d-m-Y');

        $resp = $this->json('post', route('employee.workFromHome'), [
            'date' => $date,
            'hours' => 5,
        ]);

        $resp->assertForbidden();
    }
}
