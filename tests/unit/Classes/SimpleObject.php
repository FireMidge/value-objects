<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

class SimpleObject
{
    public function __construct(public string $value) {}

    public function __toString() : string
    {
        return $this->value;
    }
}