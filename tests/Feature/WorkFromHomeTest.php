<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\WorkFromHome;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Notification;
use App\Notifications\WorkFromHomeRequestStatusChanged;

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
                'hours' => 5,
                'user_id' => $employee->id,
            ],
        );
        $this->assertEquals(WorkFromHome::first()->date, $date);
    }

    /** @test */
    public function employee_can_have_multiple_requests_to_work_from_home()
    {
        $employee = User::factory()
            ->has(
                WorkFromHome::factory()->count(3),
                'workFromHomeRequests'
            )
            ->create([
                'role_id' => Role::factory()->create(['name' => 'user']),
            ]);

        $this->assertCount(3, $employee->workFromHomeRequests);
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

        $workFromHomeRequestApproved = $employee->workFromHomeRequests->first()->approved;
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

    /** @test */
    public function when_work_from_home_request_is_approved_employee_is_notified()
    {
        Notification::fake();

        $admin = User::factory()->create([
            'role_id' => Role::factory()->create(['name' => 'admin']),
        ]);

        Passport::actingAs($admin);

        $employee = User::factory()
            ->has(
                WorkFromHome::factory()->count(3),
                'workFromHomeRequests',
            )
            ->create([
                'role_id' => Role::factory()->create(['name' => 'user']),
            ]);

        $randomWorkFromHomeRequest  = $employee->workFromHomeRequests->random();

        $resp = $this->json(
            'post',
            route(
                'workFromHome.approve',
                $randomWorkFromHomeRequest
            )
        );

        Notification::assertSentTo(
            $employee,
            function (WorkFromHomeRequestStatusChanged $notification) use ($randomWorkFromHomeRequest) {
                return $notification->request->approved;
            }
        );
    }

    /** @test */
    public function when_work_from_home_request_is_denied_employee_is_notified()
    {
        Notification::fake();

        $admin = User::factory()->create([
            'role_id' => Role::factory()->create(['name' => 'admin']),
        ]);

        Passport::actingAs($admin);

        $employee = User::factory()
            ->hasWorkFromHomeRequests(3, [
                'approved' => true,
            ])
            ->create([
                'role_id' => Role::factory()->create(['name' => 'user']),
            ]);

        $randomWorkFromHomeRequest  = $employee->workFromHomeRequests->random();

        $resp = $this->json(
            'post',
            route(
                'workFromHome.deny',
                $randomWorkFromHomeRequest
            )
        );

        Notification::assertSentTo(
            $employee,
            function (WorkFromHomeRequestStatusChanged $notification) use ($randomWorkFromHomeRequest) {
                return ! $notification->request->approved;
            }
        );
    }
}
