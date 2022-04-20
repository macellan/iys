<?php

namespace Macellan\Iys\Tests;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected string $username = 'test_username';

    protected string $password = 'test_pass';

    protected string $brandCode = 'test_brand_code';

    protected string $iysCode = 'test_iys_code';

    protected string $url = 'https://api.sandbox.iys.org.tr';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.iys.username' => $this->username,
            'services.iys.password' => $this->password,
            'services.iys.brand_code' => $this->brandCode,
            'services.iys.iys_code' => $this->iysCode,
            'services.iys.url' => $this->url,
        ]);
    }
}
