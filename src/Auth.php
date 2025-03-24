<?php

namespace Macellan\Iys;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Macellan\Iys\Enums\Config;

class Auth
{
    private int $timeout = 10;

    private string $username;

    private string $password;

    private string $endPoint;

    public function __construct(array $config)
    {
        $this->username = $config[Config::USERNAME->value];

        $this->password = $config[Config::PASSWORD->value];

        $this->endPoint = $config[Config::URL->value].'/oauth2/token';
    }

    /**
     * @throws RequestException
     */
    public function login()
    {
        return Http::timeout($this->timeout)
            ->asJson()->acceptJson()
            ->post($this->endPoint, [
                'username' => $this->username,
                'password' => $this->password,
                'grant_type' => 'password',
            ])
            ->throw();
    }
}
