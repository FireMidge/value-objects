<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\Exception\InvalidValue;
use FireMidge\ValueObject\IsFloatType;

class OddFloatWithThreeDecimalsType
{
    use IsFloatType;

    protected function transform(float $value) : float
    {
        return round($value, 3);
    }

    protected function validate(float $value) : void
    {
        if ($value % 2 === 0) {
            throw new InvalidValue(sprintf('Only odd values allowed. Value provided: "%g"', $value));
        }
    }
}