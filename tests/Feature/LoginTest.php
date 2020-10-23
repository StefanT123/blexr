<?php

namespace Tests\Feature;

class LoginTest extends AuthCase
{
    /** @test */
    public function user_cant_be_logged_in_if_the_user_doesnt_exist()
    {
        $resp = $this->json('post', route('login'), [
            'email' => '',
            'password' => 'password',
        ]);

        $resp->assertNotFound();
    }

    /** @test */
    public function status_403_is_returned_if_the_password_is_wrong()
    {
        [$user, $resp] = $this->logUserIn([], ['password' => 'wrong']);

        $resp->assertForbidden();
        $this->assertDatabaseMissing('oauth_access_tokens', ['user_id' => $user->id]);
    }

    /** @test */
    public function user_can_be_logged_in_with_correct_credentials()
    {
        [$user, $resp] = $this->logUserIn();

        $resp->assertOk();
        $this->assertDatabaseHas('oauth_access_tokens', [
            'user_id' => $user->id,
            'revoked' => 0
        ]);
    }
}
