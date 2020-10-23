<?php

namespace Tests\Feature;

use Mockery;
use App\Utilities\ProxyRequest;
use App\Listeners\DeleteOldTokens;
use Illuminate\Support\Facades\Auth;
use App\Listeners\DeleteOldRefreshTokens;

class TokenTest extends AuthCase
{
    /** @test */
    public function when_new_access_token_is_issued_correct_listener_is_called_for_the_event()
    {
        $listener = Mockery::spy(DeleteOldTokens::class);
        app()->instance(DeleteOldTokens::class, $listener);

        $user = $this->createUser();
        $proxy = new ProxyRequest;
        $resp = $proxy->grantPasswordToken($user->email, 'password');

        $listener->shouldHaveReceived('handle')->with(Mockery::on(function ($event) use ($user) {
            return $event->userId === $user->id;
        }))->once();
    }

    /** @test */
    public function when_new_access_token_is_issued_all_the_users_old_tokens_are_deleted()
    {
        $user = $this->createUser();
        $user->createToken(null);
        $initialToken = $user->tokens->first();

        $this->assertCount(1, $user->tokens);
        $this->assertDatabaseHas('oauth_access_tokens', [
            'id' => $initialToken->id,
        ]);

        $proxy = new ProxyRequest;
        $resp = $proxy->grantPasswordToken($user->email, 'password');

        $this->assertCount(1, $user->tokens);
        $this->assertDatabaseMissing('oauth_access_tokens', [
            'id' => $initialToken->id,
        ]);
        $this->assertDatabaseHas('oauth_access_tokens', [
            'id' => $user->fresh()->tokens->first()->id,
        ]);
    }

    /** @test */
    public function users_access_token_expires_in_10_minutes()
    {
        $user = $this->createUser();
        $proxy = new ProxyRequest;
        $resp = $proxy->grantPasswordToken($user->email, 'password');

        $this->assertEqualsWithDelta(10*60, $resp->expires_in, 1);
    }

    /** @test */
    public function when_user_is_logged_in_the_refresh_token_is_sent_in_cookie()
    {
        [$user, $resp] = $this->logUserIn();
        $cookies = $resp->headers->getCookies();
        $refreshTokenName = $cookies[0]->getName();

        $this->assertCount(1, $cookies);
        $this->assertEquals('refresh_token', $refreshTokenName);
    }

    /** @test */
    public function logged_in_user_can_validate_token()
    {
        $user = $this->createUser();
        $user->createToken(null);

        $resp = $this->actingAs($user, 'api')->get(route('validateToken'));

        $expiresIn = Auth::user()
            ->tokens()
            ->first()
            ->expires_at
            ->diffInSeconds(\Carbon\Carbon::now());

        $resp->assertOk();
        $resp->assertSeeText($user->email);
        $resp->assertSeeText($expiresIn);
    }

    /** @test */
    public function user_can_refresh_access_token()
    {
        [$user, $response] = $this->logUserIn();

        $token = $response->original['token'];
        $refreshToken = $response->headers->getCookies()[0]->getValue();

        $resp = $this->call('post', route('refreshToken'), [], [
            'refresh_token' => $refreshToken,
        ]);

        $resp->assertOk();
        $resp->assertJson($resp->json());
    }

    /** @test */
    public function when_new_refresh_token_is_issued_correct_listener_is_called_for_the_event()
    {
        $listener = Mockery::spy(DeleteOldRefreshTokens::class);
        app()->instance(DeleteOldRefreshTokens::class, $listener);

        [$user, $response] = $this->logUserIn();

        $token = $response->original['token'];
        $refreshToken = $response->headers->getCookies()[0]->getValue();

        $resp = $this->call('post', route('refreshToken'), [], [
            'refresh_token' => $refreshToken,
        ]);

        $listener->shouldHaveReceived('handle');
    }

    /** @test */
    public function when_new_refresh_token_is_issued_the_expired_ones_are_deleted()
    {
        \DB::table('oauth_refresh_tokens')->insert([
            'id' => 'test',
            'access_token_id' => 'test_access_token_id',
            'revoked' => 0,
            'expires_at' => now(),
        ]);

        $this->assertCount(1, \DB::table('oauth_refresh_tokens')->get());
        $this->assertDatabaseHas('oauth_refresh_tokens', [
            'id' => 'test',
        ]);

        [$user, $response] = $this->logUserIn();

        $this->assertCount(1, \DB::table('oauth_refresh_tokens')->get());
        $this->assertDatabaseMissing('oauth_refresh_tokens', [
            'id' => 'test',
        ]);
    }
}
