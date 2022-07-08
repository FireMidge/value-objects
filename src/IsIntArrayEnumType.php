<?php
declare(strict_types=1);

namespace FireMidge\ValueObject;

use FireMidge\ValueObject\Exception\InvalidValue;

/**
 * This type is similar to IsIntEnumType with the exception that instead of holding a single value,
 * this can hold multiple values. Each value must be one of a list of valid values.
 *
 * You can override areValuesUnique() to return true, which means any integers passed to this class
 * must be unique.
 *
 * @method static withValue(int $addedValue)
 * @method static tryWithoutValue(int $value)
 * @method static contains(int $value)
 */
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