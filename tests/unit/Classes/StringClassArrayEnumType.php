<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\IsClassArrayEnumType;

class StringClassArrayEnumType
{
    use IsClassArrayEnumType {
        // Aliasing the methods to be able to make them public.
        // This is ONLY to test them separately - otherwise this wouldn't make any sense
        IsClassArrayEnumType::convertIntoInstance as private _convertIntoInstance;
        IsClassArrayEnumType::shouldAllowToStringConversion as private _shouldAllowToStringConversion;
    }

    public static function convertIntoInstance(mixed $value, string $className) : object
    {
        return static::_convertIntoInstance($value, $className);
    }

    public static function shouldAllowToStringConversion() : bool
    {
        return static::_shouldAllowToStringConversion();
    }

    protected static function className() : string
    {
        return StringEnumType::class;
    }
}
