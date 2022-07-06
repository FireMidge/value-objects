<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\Exception\InvalidValue;
use FireMidge\ValueObject\IsIntType;

/**
 * Integer type with both a minimum and maximum value.
 */
class OddIntType
{
    use IsIntType;

    protected function validate(int $value) : void
    {
        if ($value % 2 === 0) {
            throw new InvalidValue(sprintf('Only odd values allowed. Value provided: %d', $value));
        }
    }
}