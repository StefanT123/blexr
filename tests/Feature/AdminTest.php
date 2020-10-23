<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\License;
use Laravel\Passport\Passport;
use App\Mail\EmployeeLoginDetails;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\WithFaker;

class AdminTest extends TestCase
{
    use WithFaker;

    /** @test */
    public function admin_can_add_new_employee()
    {
        [$resp, $employee] = $this->createEmployee([
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ]);

        $resp->assertSuccessful();
        $this->assertDatabaseHas('users', ['email' => $employee['email']]);
    }

    /** @test */
    public function email_is_sent_when_new_employee_is_created()
    {
        Mail::fake();

        [$resp, $employee] = $this->createEmployee([
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ]);

        $resp->assertSuccessful();

        Mail::assertSent(function (EmployeeLoginDetails $mail) use ($employee) {
            return $mail->employee->name === $employee['name'];
        });
    }

    /** @test */
    public function when_new_employee_is_added_password_is_automatically_generated()
    {
        [$resp, $employee] = $this->createEmployee([
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ]);

        $createdEmployee = User::where('email', $employee['email'])->first();

        $resp->assertSuccessful();
        $this->assertFalse(Hash::check('secret', $createdEmployee));
    }

    /** @test */
    public function admin_can_create_employee_without_providing_password()
    {
        [$resp, $employee] = $this->createEmployee();

        $resp->assertSuccessful();
        $this->assertDatabaseHas('users', ['email' => $employee['email']]);
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

        $resp = $this->post(route('employee.licenses', $employee), ['ids' => $licenses]);

        $resp->assertSuccessful();
        $this->assertCount(5, $employee->licenses);
    }

    /** @test */
    public function admin_can_set_license_as_completed_for_the_employee()
    {
        $admin = User::factory()->create([
            'role_id' => Role::factory()->create(['name' => 'admin']),
        ]);

        $employee = User::factory()
            ->hasAttached(
                License::factory()->count(3),
                ['completed' => false],
            )
            ->create([
                'role_id' => Role::factory()->create(['name' => 'user']),
            ]);

        Passport::actingAs($admin);

        $randomLicense = $employee->licenses()->inRandomOrder()->first();

        $resp = $this->post(route('employee.license.complete', [$employee, $randomLicense]));

        $resp->assertOk();
        $completedLicense = $employee->licenses()
            ->where('id', $randomLicense->id)
            ->first();

        $this->assertTrue((bool) $completedLicense->pivot->completed);
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

        $resp = $this->post(
            route('employee.create'),
            array_merge($employee, $fields)
        );

        return [$resp, $employee];
    }
}
