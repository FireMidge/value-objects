<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\IsStringType;

class CapitalStringType
{
    use IsStringType;

    protected function transform(string $value) : string
    {
        return $this->trimAndCapitalise($value);
    }

    protected function validate(string $value): void
    {
        $this->validateLength($value, 2, 3);
    }
}