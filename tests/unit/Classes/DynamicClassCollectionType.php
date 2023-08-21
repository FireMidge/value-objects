<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\IsClassCollectionType;

class DynamicClassCollectionType
{
    use IsClassCollectionType;

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