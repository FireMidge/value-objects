<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

/**
 * A string object that returns a number.
 */
class SimpleBadObject
{
    public function __construct(public string $value) {}

    public function toString() : int
    {
        return (int) $this->value;
    }
}