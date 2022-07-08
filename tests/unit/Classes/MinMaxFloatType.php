<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\IsFloatType;

/**
 * Float type with both a minimum and maximum value.
 */
class MinMaxFloatType
{
    use IsFloatType;

    protected static function minValidValue() : ?float
    {
        return 11.111;
    }

    protected static function maxValidValue() : ?float
    {
        return 55.555;
    }
}