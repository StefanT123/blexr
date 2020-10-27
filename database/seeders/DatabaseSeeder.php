<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\License;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'email' => 'admin@admin.com',
            'role_id' => Role::factory()->create(['name' => 'admin'])->id,
        ]);

        $licenses = [
            'Microsoft Office License',
            'Email Access Granted',
            'Git Repository Granted',
            'Jira Access Granted',
        ];

        foreach ($licenses as $license) {
            License::factory()->create([
                'name' => $license,
            ]);
        }
    }
}
