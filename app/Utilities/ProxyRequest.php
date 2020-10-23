<?php

namespace App\Utilities;

use App\Models\User;

class ProxyRequest
{
    /**
     * Grant access token to a user with custom
     * passport password grant.
     *
     * @param  string $email
     * @param  string $password
     * @return object
     */
    public function grantPasswordToken(string $email, string $password)
    {
        $params = [
            'grant_type' => 'password',
            'username' => $email,
            'password' => $password,
        ];

        return $this->makePostRequest($params);
    }

    /**
     * Refresh users access token.
     *
     * @return object
     */
    public function refreshAccessToken()
    {
        $refreshToken = request()->cookie('refresh_token');

        abort_unless($refreshToken, 403, 'Your refresh token is expired.');

        $params = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'scope' => '',
        ];

        return $this->makePostRequest($params);
    }

    /**
     * Make post request to passports routes.
     *
     * @param  array  $params
     * @return object
     */
    protected function makePostRequest(array $params)
    {
        $params = array_merge([
            'client_id' => config('services.passport.password_client_id'),
            'client_secret' => config('services.passport.password_client_secret'),
            'scope' => '*',
        ], $params);

        $proxy = \Request::create('oauth/token', 'post', $params);
        $resp = json_decode(app()->handle($proxy)->getContent());

        $this->setHttpOnlyCookie($resp->refresh_token);

        return $resp;
    }

    /**
     * Set http_only cookie in the response.
     * Meaning that once it's sent to the browser,
     * with the response, it will be sent by the browser
     * with every request.
     *
     * @param  string $refreshToken
     * @return void
     */
    protected function setHttpOnlyCookie(string $refreshToken)
    {
        cookie()->queue(
            'refresh_token',
            $refreshToken,
            14400, // 10 days
            null,
            null,
            false,
            true
        );
    }
}
