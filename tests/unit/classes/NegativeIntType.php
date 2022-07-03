<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\classes;

use FireMidge\ValueObject\IsIntType;

class NegativeIntType
{
    use IsIntType;

    protected static function minValidValue() : ?int
    {
        return null;
    }
}