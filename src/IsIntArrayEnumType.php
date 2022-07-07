<?php
declare(strict_types=1);

namespace FireMidge\ValueObject;

use FireMidge\ValueObject\Exception\InvalidValue;

trait IsIntArrayEnumType
{
    use IsArrayEnumType;

    protected function validateEach($value) : void
    {
        if (! is_int($value)) {
            throw InvalidValue::invalidType($value, 'int');
        }
    }
}