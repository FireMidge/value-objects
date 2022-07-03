<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\classes;

use FireMidge\ValueObject\IsIntType;

/**
 * Integer type with both a minimum and maximum value.
 */
class MinMaxIntType
{
    use IsIntType;

    protected static function minValidValue() : ?int
    {
        return 123;
    }

    protected static function maxValidValue() : ?int
    {
        return 567;
    }
}