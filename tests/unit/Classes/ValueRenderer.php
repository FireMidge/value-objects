<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\Exception\RendersValue;

class ValueRenderer
{
    use RendersValue {
        RendersValue::renderValue as private _renderValue;
    }

    public static function renderValue(mixed $value) : string
    {
        return static::_renderValue($value);
    }
}