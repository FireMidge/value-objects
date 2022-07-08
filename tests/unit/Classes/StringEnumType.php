<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\IsStringEnumType;

class StringEnumType
{
    use IsStringEnumType;

    public const SPRING = 'spring';
    public const SUMMER = 'summer';
    public const AUTUMN = 'autumn';
    public const WINTER = 'winter';

    public static function all() : array
    {
        return [
            self::SPRING,
            self::SUMMER,
            self::AUTUMN,
            self::WINTER,
        ];
    }

    public static function spring() : self
    {
        return new static(static::SPRING);
    }

    public static function summer() : self
    {
        return new static(static::SUMMER);
    }

    public static function autumn() : self
    {
        return new static(static::AUTUMN);
    }

    public static function winter() : self
    {
        return new static(static::WINTER);
    }
}