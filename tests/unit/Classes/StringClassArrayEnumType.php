<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\IsClassArrayEnumType;

class StringClassArrayEnumType
{
    use IsClassArrayEnumType;

    protected static function className() : string
    {
        return StringEnumType::class;
    }
}
