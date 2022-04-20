<?php

namespace Macellan\Iys;

use Exception;
use Illuminate\Http\Client\RequestException;
use Macellan\Iys\Drivers\Permission\PermissionDriver;
use Macellan\Iys\Enums\Config;

class IysManager
{
    private ?array $config;

    private string $bearer;

    /**
     * @throws RequestException
     * @throws Exception
     */
    public function __construct()
    {
        if (!  $this->config = config('services.iys')) {
            throw new Exception('Iys configuration not found.');
        }

        if ($diff = array_diff(Config::toArray(), array_keys($this->config))) {
            throw new Exception(implode(',', $diff) . ' configuration parameter not found.');
        }

        $result = (new Auth($this->config))->login();

        $this->bearer = $result->json('access_token') ?? null;
    }

    public function createPermissionDriver(): PermissionDriver
    {
        return new PermissionDriver($this->config, $this->bearer);
    }

    public static function make(): IysManager
    {
        return new static();
    }
}
