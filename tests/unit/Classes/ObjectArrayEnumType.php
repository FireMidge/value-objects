<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\Exception\InvalidValue;
use FireMidge\ValueObject\IsArrayEnumType;

class ObjectArrayEnumType
{
    use IsArrayEnumType;

    protected static function all() : array
    {
        return [
            new SimpleObject('A'),
            new SimpleObject('B'),
            new SimpleObject('C'),
        ];
    }

    protected function validateEach($value) : void
    {
        if (! is_object($value) || (! $value instanceof SimpleObject)) {
            throw InvalidValue::notInstanceOf($value, SimpleObject::class);
        }
    }
}