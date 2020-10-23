<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class AuthCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        \Artisan::call('passport:install');
        $passwordClientSecret = \DB::table('oauth_clients')
            ->where('password_client', 1)
            ->value('secret');
        config(['services.passport.password_client_id' => 2]);
        config(['services.passport.password_client_secret' => $passwordClientSecret]);
    }

    public function createUser(array $params = [])
    {
        return User::factory()->create($params);
    }

    protected function logUserIn(array $userParams = [], array $requestParams = [])
    {
        $user = $this->createUser($userParams);
        $resp = $this->json('post', route('login'), array_merge([
            'email' => $user->email,
            'password' => 'password',
        ], $requestParams));

        return [$user, $resp];
    }
}
