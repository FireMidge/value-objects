<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\Helper\CanCreateInstance;
use FireMidge\ValueObject\IsClassArrayEnumType;

class DynamicClassArrayEnumType
{
    use IsClassArrayEnumType;

    use CanCreateInstance {
        // Aliasing the methods to be able to make them public.
        // This is ONLY to test them separately - otherwise this wouldn't make any sense
        CanCreateInstance::convertIntoInstance as private _convertIntoInstance;
    }

    private static string $className = SimpleStringType::class;
    private static bool $allowStringConversion = false;

    public static function useClass(string $classFqn) : void
    {
        static::$className = $classFqn;
    }

    public static function allowToStringConversion(bool $value) : void
    {
        static::$allowStringConversion = $value;
    }

    public static function convertIntoInstance(mixed $value, string $className) : object
    {
        return static::_convertIntoInstance($value, $className);
    }

    protected static function className() : string
    {
        return static::$className;
    }

    protected static function areValuesUnique() : bool
    {
        return false;
    }

    protected static function shouldAllowToStringConversion() : bool
    {
        return static::$allowStringConversion;
    }
}