<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

class BoolType
{
    public function __construct(private bool $value) {}

    public static function fromBool(bool $value) : static
    {
        return new static($value);
    }

    public function __toString() : string
    {
        return (string) $this->value;
    }
}