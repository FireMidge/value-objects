<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\Helper\CanExtractValueOfType;
use FireMidge\ValueObject\IsStringEnumType;

class StringEnumType
{
    use IsStringEnumType;

    /**
     * Imported separately (to avoid additional false positives in mutation test result).
     */
    use CanExtractValueOfType;

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

    public static function spring() : static
    {
        return new static(static::SPRING);
    }

    public static function summer() : static
    {
        return new static(static::SUMMER);
    }

    public static function autumn() : static
    {
        return new static(static::AUTUMN);
    }

    public static function winter() : static
    {
        return new static(static::WINTER);
    }
}