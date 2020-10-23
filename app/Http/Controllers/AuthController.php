<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utilities\ProxyRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;

class AuthController
{
    /**
     * ProxyRequest instance.
     *
     * @var \App\Utilities\ProxyRequest
     */
    protected $proxy;

    /**
     * Create instance of this class.
     *
     * @param \App\Utilities\ProxyRequest $proxy
     */
    public function __construct(ProxyRequest $proxy)
    {
        $this->proxy = $proxy;
    }

    /**
     * User can be logged in.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    public function login()
    {
        $user = User::where('email', request('email'))->first();

        abort_unless($user, 404, 'This combination does not exists.');
        abort_unless(
            \Hash::check(request('password'), $user->password),
            403,
            'This combination does not exists.'
        );

        $resp = $this->proxy
            ->grantPasswordToken($user->email, request('password'));

        return response([
            'user' => new UserResource($user),
            'token' => $resp->access_token,
            'expiresIn' => $resp->expires_in,
            'message' => 'You have been logged in',
        ], 200);
    }

    /**
     * Checks if the token is valid.
     *
     * It does that by guarding the route
     * with the auth:api middleware, so if the
     * user with token passes the middleware, and
     * gets in this function, it means that the
     * token is valid.
     *
     * @return array
     */
    public function validateToken()
    {
        $user = Auth::user();
        $expiresIn = $user
            ->tokens()
            ->first()
            ->expires_at
            ->diffInSeconds(\Carbon\Carbon::now());

        return [
            'user' => new UserResource($user),
            'expiresIn' => $expiresIn,
        ];
    }

    /**
     * Refresh users token.
     *
     * @return \Illuminate\Http\Response
     */
    public function refreshToken()
    {
        $resp = $this->proxy->refreshAccessToken();

        return response([
            'token' => $resp->access_token,
            'expiresIn' => $resp->expires_in,
            'message' => 'Token has been refreshed.',
        ], 200);
    }

    /**
     * Logout current user
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    public function logout()
    {
        if (request()->user()) {
            $token = request()->user()->token();
            $token->delete();
        }

        abort_unless(request()->cookie('refresh_token'), 401, 'Unauthorized');

        cookie()->queue(cookie()->forget('refresh_token'));

        return response([
            'message' => 'You have been successfully logged out',
        ], 200);
    }
}
