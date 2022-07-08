<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\IsFloatType;

class NegativeFloatType
{
    use IsFloatType;

    protected static function minValidValue() : ?int
    {
        return null;
    }
}