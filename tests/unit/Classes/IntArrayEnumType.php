<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\IsIntArrayEnumType;

/**
 * May be useful when having integer flags, and you allow
 * a combination of them.
 */
class IntArrayEnumType
{
    use IsIntArrayEnumType;

    protected static function all() : array
    {
        return [
            11,
            22,
            33,
        ];
    }
}