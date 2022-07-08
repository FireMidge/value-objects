<?php
declare(strict_types=1);

namespace FireMidge\ValueObject;

use FireMidge\ValueObject\Exception\InvalidValue;

/**
 * This type is similar to IsStringEnumType with the exception that instead of holding a single value,
 * this can hold multiple values. Each value must be one of a list of valid values.
 *
 * This type is particularly useful for a FieldList: the "fields" query parameter of a RESTful API.
 *
 * You can override areValuesUnique() to return true, which means any strings passed to this class
 * must be unique.
 *
 * @method static withValue(string $addedValue)
 * @method static tryWithoutValue(string $value)
 * @method static contains(string $value)
 */
trait IsStringArrayEnumType
{
    use IsArrayEnumType;

    protected function validateEach($value) : void
    {
        if (! is_string($value)) {
            throw InvalidValue::invalidType($value, 'string');
        }
    }
}