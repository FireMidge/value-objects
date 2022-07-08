<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\IsStringType;

class LowerCaseStringType
{
    use IsStringType;

    protected function transform(string $value) : string
    {
        return $this->trimAndLowerCase($value);
    }

    protected function validate(string $value) : void
    {
        $this->validateLength($value, 5);
    }
}