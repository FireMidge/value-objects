<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\IsIntType;

/**
 * Int type with only a maximum value and an unlimited minimum value.
 */
class MaxOnlyIntType
{
    use IsIntType;

    protected static function maxValidValue() : ?float
    {
        return -1;
    }

    protected static function minValidValue() : ?float
    {
        return null;
    }
}