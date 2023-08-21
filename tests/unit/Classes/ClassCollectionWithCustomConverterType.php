<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\Exception\ConversionError;
use FireMidge\ValueObject\IsClassCollectionType;

class ClassCollectionWithCustomConverterType
{
    use IsClassCollectionType {
        IsClassCollectionType::convertFromRaw as private _convertFromRaw;
    }

    protected static function className() : string
    {
        return PrivateConstructorObject::class;
    }

    protected static function convertFromRaw(mixed $value) : object
    {
        try {
            return static::_convertFromRaw($value);
        } catch (ConversionError) {
            return PrivateConstructorObject::fromCustom(substr($value, strrpos($value, '.')+1));
        }
    }
}