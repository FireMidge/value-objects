<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\IsIntEnumType;

class IntEnumType
{
    use IsIntEnumType;

    public const SPRING = 1;
    public const SUMMER = 2;
    public const AUTUMN = 3;
    public const WINTER = 4;

    public function all() : array
    {
        return [
            self::SPRING,
            self::SUMMER,
            self::AUTUMN,
            self::WINTER,
        ];
    }
}