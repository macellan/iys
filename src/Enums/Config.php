<?php

namespace Macellan\Iys\Enums;

enum Config: string
{
    case USERNAME = 'username';
    case PASSWORD = 'password';
    case IYS_CODE = 'iys_code';
    case BRAND_CODE = 'brand_code';
    case URL = 'url';

    public static function toArray(): array
    {
        return [
            self::USERNAME->value,
            self::PASSWORD->value,
            self::IYS_CODE->value,
            self::BRAND_CODE->value,
            self::URL->value,
        ];
    }
}
