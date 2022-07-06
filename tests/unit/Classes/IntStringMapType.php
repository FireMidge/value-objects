<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\IsIntStringMapType;

class IntStringMapType
{
    use IsIntStringMapType;

    protected static function provideMap(): array
    {
        return [
            1 => 'spring',
            2 => 'summer',
            3 => 'autumn',
            4 => 'winter',
        ];
    }
}