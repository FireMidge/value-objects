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
        // Using the modulus operand (%) only works with integers. Floats are implicitly cast to integers.
        // fmod() allows us to keep the values as a float without integer conversion.
        if (fmod($value, 2.0) === 0.0) {
            throw new InvalidValue(sprintf('Only odd values allowed. Value provided: "%g"', $value));
        }
    }
}