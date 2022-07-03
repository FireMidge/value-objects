<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\classes;

use FireMidge\ValueObject\IsStringEnumType;

class StringEnumType
{
    use IsStringEnumType;

    public const SPRING = 'spring';
    public const SUMMER = 'summer';
    public const AUTUMN = 'autumn';
    public const WINTER = 'winter';

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