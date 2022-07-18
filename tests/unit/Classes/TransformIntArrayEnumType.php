<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\IsIntArrayEnumType;

/**
 * Allows passing a numeric string which gets
 * converted to an integer before validating.
 */
class TransformIntArrayEnumType
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

    protected function transformEach($value)
    {
        if (! is_numeric($value)) {
            return $value;
        }

        return (int) $value;
    }


}