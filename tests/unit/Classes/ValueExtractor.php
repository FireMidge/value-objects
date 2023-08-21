<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\Helper\CanExtractValueOfType;

/**
 * Used to test the CanExtractValueOfOther trait.
 * The only way to ensure test coverage (in the coverage report) is by exposing the methods as public.
 */
class ValueExtractor
{
    use CanExtractValueOfType {
        // Aliasing the methods to be able to make them public.
        // This is ONLY to test them separately - otherwise this wouldn't make any sense,
        // especially not as non-static methods...
        CanExtractValueOfType::getStringValueOfOther as private _getStringValueOfOther;
        CanExtractValueOfType::getIntValueOfOther as private _getIntValueOfOther;
        CanExtractValueOfType::getFloatValueOfOther as private _getFloatValueOfOther;
    }

    public function getStringValueOfOther(mixed $other) : string
    {
        return $this->_getStringValueOfOther($other);
    }

    public function getIntValueOfOther(mixed $other) : int
    {
        return $this->_getIntValueOfOther($other);
    }

    public function getFloatValueOfOther(mixed $other) : float
    {
        return $this->_getFloatValueOfOther($other);
    }
}