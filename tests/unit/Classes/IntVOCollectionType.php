<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\Exception\InvalidValue;
use FireMidge\ValueObject\IsCollectionType;

/**
 * Collection of Integer Value Objects (VO).
 */
class IntVOCollectionType
{
    use IsCollectionType;

    protected function validateEach($value) : void
    {
        if (! is_object($value) || ! $value instanceof MinMaxIntType) {
            throw InvalidValue::notInstanceOf($value, MinMaxIntType::class);
        }
    }
}