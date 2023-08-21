<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

class DoubleObject
{
    public function __construct(private float $value) {}

    public static function fromDouble(float $value) : static
    {
        return new static($value);
    }

    public function toDouble() : float
    {
        return $this->value;
    }
}