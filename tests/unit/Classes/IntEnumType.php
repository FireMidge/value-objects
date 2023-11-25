<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\Helper\CanExtractValueOfType;
use FireMidge\ValueObject\IsIntEnumType;

class IntEnumType
{
    use IsIntEnumType;

    /**
     * Imported separately (without this, there are more false positives in the mutation test result).
     */
    use CanExtractValueOfType;

    public const SPRING = 1;
    public const SUMMER = 2;
    public const AUTUMN = 3;
    public const WINTER = 4;

    public static function all() : array
    {
        return [
            self::SPRING,
            self::SUMMER,
            self::AUTUMN,
            self::WINTER,
        ];
    }
}