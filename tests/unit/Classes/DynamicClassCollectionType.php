<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\IsClassCollectionType;

class DynamicClassCollectionType
{
    use IsClassCollectionType;

    private static string $className = SimpleStringType::class;

    public static function useClass(string $classFqn) : void
    {
        static::$className = $classFqn;
    }

    protected static function className() : string
    {
        return static::$className;
    }

    protected static function areValuesUnique() : bool
    {
        return false;
    }
}