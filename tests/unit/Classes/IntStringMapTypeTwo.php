<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\IsIntStringMapType;

class IntStringMapTypeTwo
{
    use IsIntStringMapType;

    protected static function provideMap() : array
    {
        return [
            1 => 'winter',
            2 => 'autumn',
            3 => 'summer',
            4 => 'spring',
        ];
    }
}