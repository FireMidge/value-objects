<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\Exception\InvalidValue;
use FireMidge\ValueObject\IsArrayEnumType;

/**
 * This is an array enum type, with each value
 * being a string type value object (VO).
 */
class StringVOArrayEnumType
{
    use IsArrayEnumType;

    protected static function all() : array
    {
        return array_map(function($value) {
            return StringEnumType::fromString($value);
        }, StringEnumType::all());
    }

    protected function validateEach($value) : void
    {
        if (! is_object($value) || (! $value instanceof StringEnumType)) {
            throw InvalidValue::notInstanceOf($value, StringEnumType::class);
        }
    }

    protected static function areValuesUnique() : bool
    {
        return true;
    }
}