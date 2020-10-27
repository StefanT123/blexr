<?php

namespace Tests\Feature;

use Laravel\Passport\Passport;

class LogoutTest extends AuthCase
{
    /** @test */
    public function exception_is_thrown_if_not_logged_user_tries_to_call_logout()
    {
        $resp = $this->json('post', route('logout'));

        $resp->assertUnauthorized();
    }

    /** @test */
    public function logged_user_can_logout()
    {
        $user = $this->createUser();

        Passport::actingAs($user);
        $resp = $this->call('post', route('logout'), [], [
            'refresh_token' => 'some-token',
        ]);

        $resp->assertOk();
        $resp->assertSeeText('You have been successfully logged out');
    }

    /** @test */
    public function when_user_is_logged_out_refresh_token_cookie_is_deleted()
    {
        $user = $this->createUser();

        Passport::actingAs($user);
        $resp = $this->call('post', route('logout'), [], [
            'refresh_token' => 'some-token',
        ]);

        $resp->assertOk();

        $cookies = $resp->headers->getCookies();
        $refreshTokenValue = $cookies[0]->getValue();
        $this->assertNull($refreshTokenValue);
    }
}
