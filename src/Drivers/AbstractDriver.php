<?php

namespace Macellan\Iys\Drivers;

use Macellan\Iys\Enums\Config;

abstract class AbstractDriver
{
    protected string $branchCode;

    protected string $iysCode;

    protected string $bearer;

    protected string $baseUrl;

    public function __construct(array $config, string $bearer)
    {
        $this->branchCode = $config[Config::BRAND_CODE->value];

        $this->iysCode = $config[Config::IYS_CODE->value];

        $this->baseUrl = $config[Config::URL->value];

        $this->bearer = $bearer;
    }
}
