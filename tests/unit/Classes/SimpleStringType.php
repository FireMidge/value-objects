<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\IsStringType;

class SimpleStringType
{
    use IsStringType {
        // Aliasing the methods to be able to make them public.
        // This is ONLY to test them separately - otherwise this wouldn't make any sense,
        // especially not as non-static methods...
        IsStringType::validateLength as _validateLength;
        IsStringType::validateEmailAddress as _validateEmailAddress;
    }

    public function validateLength(string $value, ?int $minLength = null, ?int $maxLength = null) : void
    {
        $this->_validateLength($value, $minLength, $maxLength);
    }

    public function validateEmailAddress(string $value) : void
    {
        $this->_validateEmailAddress($value);
    }
}