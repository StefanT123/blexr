<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use Laravel\Passport\Passport;

class LicenseTest extends TestCase
{
    /** @test */
    public function admin_can_add_new_license()
    {
        $admin = User::factory()->create([
            'role_id' => Role::factory()->create(['name' => 'admin']),
        ]);

        Passport::actingAs($admin);

        $resp = $this->post(route('license.create'), [
            'name' => 'New License',
        ]);

        $resp->assertSuccessful();
        $this->assertDatabaseHas('licenses', ['name' => 'New License']);
    }
}
