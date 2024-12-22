<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\IsFloatType;

/**
 * Float type with only a maximum value and an unlimited minimum value.
 */
class MaxOnlyFloatType
{
    use IsFloatType;

    protected static function maxValidValue() : ?float
    {
        return 88.888;
    }

    protected static function minValidValue() : ?float
    {
        return null;
    }
}