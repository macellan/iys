<?php

namespace Macellan\Iys\Tests;

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Macellan\Iys\Auth;

class AuthTest extends TestCase
{
    /**
     * @throws RequestException
     */
    public function test_login()
    {
        $endpoint = '/oauth2/token';

        Http::fake([
            $endpoint => Http::response([
                'access_token' => 'eyJhbGciOiJSUzI1NiIsInR5c',
                'refresh_token' => 'eyJhbGciOiJSUzI1NiIsInR5c',
                'expires_in' => 7200,
                'refresh_expires_in' => 14400,
                'token_type' => 'bearer',
            ]),
        ]);

        $config = [
            'username' => $this->username,
            'password' => $this->password,
            'url' => $this->url,
        ];

        (new Auth($config))->login();

        Http::assertSent(function (Request $request) use ($endpoint) {
            return $request->url() == $this->url.$endpoint &&
                $request['username'] == $this->username &&
                $request['password'] == $this->password &&
                $request['grant_type'] == 'password';
        });
    }

    /**
     * @return void
     *
     * @throws RequestException
     */
    public function test_throws_an_exception()
    {
        Http::fake([
            '/oauth2/token' => Http::response([
                'error' => 'invalid_request',
                'error_description' => 'Giriş yöntemi belirtilmemiş.',
            ], 400),
        ]);

        $this->expectException(RequestException::class);

        $config = [
            'username' => $this->username,
            'password' => $this->password,
            'url' => $this->url,
        ];

        (new Auth($config))->login();
    }
}
