<?php

declare(strict_types=1);

namespace Yurunsoft\Nacos\Util;

class StringUtil
{
    private function __construct()
    {
    }

    public static function convertBoolToString(bool $value): string
    {
        return $value ? 'true' : 'false';
    }
}
