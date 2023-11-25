<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

enum CustomEnumWithPrivateAllType
{
    protected static function all() : array
    {
        return [100, 101, 102, 103];
    }
}
