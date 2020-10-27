<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\License;
use App\Models\WorkFromHome;
use Laravel\Passport\Passport;
use App\Mail\EmployeeLoginDetails;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\WithFaker;

class AdminTest extends TestCase
{
    use WithFaker;

    /** @test */
    public function admin_can_see_all_employees()
    {
        $admin = User::factory()->create([
            'role_id' => Role::factory()->create(['name' => 'admin']),
        ]);

        $employees = User::factory()->count(5)->create([
            'role_id' => Role::factory()->create(['name' => 'user']),
        ]);

        Passport::actingAs($admin);

        $resp = $this->json('get', route('employee.index'));

        $resp->assertOk();
        $this->assertCount(5, $resp->json()['employees']);
    }

    /** @test */
    public function admin_can_add_new_employee()
    {
        $employee = $this->createEmployee([
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ]);

        $this->assertDatabaseHas('users', ['email' => $employee['email']]);
    }

    /** @test */
    public function email_is_sent_when_new_employee_is_created()
    {
        Mail::fake();

        $employee = $this->createEmployee([
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ]);

        Mail::assertSent(function (EmployeeLoginDetails $mail) use ($employee) {
            return $mail->employee->name === $employee['name'];
        });
    }

    /** @test */
    public function when_new_employee_is_added_password_is_automatically_generated()
    {
        $employee = $this->createEmployee([
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ]);

        $createdEmployee = User::where('email', $employee['email'])->first();

        $this->assertFalse(Hash::check('secret', $createdEmployee));
    }

    /** @test */
    public function admin_can_create_employee_without_providing_password()
    {
        $employee = $this->createEmployee();

        $this->assertDatabaseHas('users', ['email' => $employee['email']]);
    }

    /** @test */
    public function admin_can_see_all_licenses()
    {
        $admin = User::factory()->create([
            'role_id' => Role::factory()->create(['name' => 'admin']),
        ]);

        Passport::actingAs($admin);

        $licenses = License::factory()
            ->count(5)
            ->create();

        $resp = $this->json('get', route('license.index'));

        $resp->assertOk();
        $this->assertCount(5, $resp->json()['licenses']);
    }

    /** @test */
    public function admin_can_grant_licenses_to_employee()
    {
        $admin = User::factory()->create([
            'role_id' => Role::factory()->create(['name' => 'admin']),
        ]);

        $employee = User::factory()->create([
            'role_id' => Role::factory()->create(['name' => 'user']),
        ]);

        Passport::actingAs($admin);

        $licenses = License::factory()
            ->count(5)
            ->create()
            ->pluck('id')
            ->toArray();

        $resp = $this->post(
            route('employee.licenses', $employee),
            ['ids' => $licenses]
        );

        $resp->assertSuccessful();
        $this->assertCount(5, $employee->licenses);
    }

    /** @test */
    public function admin_can_see_every_work_from_home_request()
    {
        $admin = User::factory()->create([
            'role_id' => Role::factory()->create(['name' => 'admin']),
        ]);

        Passport::actingAs($admin);

        $employees = User::factory()
            ->count(5)
            ->has(
                WorkFromHome::factory()->count(3),
                'workFromHomeRequests',
            )
            ->create([
                'role_id' => Role::factory()->create(['name' => 'user']),
            ]);

        $resp = $this->json('get', route('workFromHome.index'));

        $resp->assertOk();
        $resp->assertJsonFragment([
            'date' => WorkFromHome::inRandomOrder()->first()->date,
        ]);
        $this->assertCount(15, $resp->json()['requests']);
    }

    /** @test */
    public function admin_can_filter_work_from_home_requests_by_user()
    {
        $admin = User::factory()->create([
            'role_id' => Role::factory()->create(['name' => 'admin']),
        ]);

        Passport::actingAs($admin);

        $employees = User::factory()
            ->count(5)
            ->has(
                WorkFromHome::factory()
                    ->count($randNumber = $this->faker->numberBetween(1, 10)),
                'workFromHomeRequests',
            )
            ->create([
                'role_id' => Role::factory()->create(['name' => 'user']),
            ]);

        $randomEmployee = $employees->random();

        $resp = $this->json('get', route('workFromHome.show', $randomEmployee));

        $resp->assertOk();
        $this->assertCount($randNumber, $resp->json()['requests']);
    }

    /** @test */
    public function admin_can_approve_work_from_home_request()
    {
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

        $randomWorkFromHomeRequest  = $employee->workFromHomeRequests->first();
        $this->assertFalse((bool) $randomWorkFromHomeRequest->approved);

        $resp = $this->json(
            'post',
            route(
                'workFromHome.approve',
                $randomWorkFromHomeRequest
            )
        );

        $resp->assertOk();
        $this->assertTrue((bool) $randomWorkFromHomeRequest->fresh()->approved);
    }

    /** @test */
    public function admin_can_deny_work_from_home_request()
    {
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
        $this->assertTrue((bool) $randomWorkFromHomeRequest->approved);

        $resp = $this->json(
            'post',
            route(
                'workFromHome.deny',
                $randomWorkFromHomeRequest
            )
        );

        $resp->assertOk();
        $this->assertFalse((bool) $randomWorkFromHomeRequest->fresh()->approved);
    }

    protected function createEmployee(array $fields = [])
    {
        $admin = User::factory()->create([
            'role_id' => Role::factory()->create(['name' => 'admin']),
        ]);

        Passport::actingAs($admin);

        $employee = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
        ];

        $resp = $this->json(
            'post',
            route('employee.create'),
            array_merge($employee, $fields)
        );

        $resp->assertSuccessful();

        return $employee;
    }
}
