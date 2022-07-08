<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\IsStringType;

class UpperCaseStringType
{
    use IsStringType;

    protected function transform(string $value) : string
    {
        return $this->trimAndUpperCase($value);
    }

    protected function validate(string $value) : void
    {
        $this->validateLength($value, null, 2);
    }
}