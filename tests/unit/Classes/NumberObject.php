<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

class NumberObject
{
    public function __construct(private float|int $value) {}

    public function toNumber() : float|int
    {
        return $this->value;
    }
}