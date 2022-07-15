<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\Exception\InvalidValue;
use FireMidge\ValueObject\Helper\CanTransformStrings;
use FireMidge\ValueObject\IsCollectionType;

class StringCollectionOriginalCasingType
{
    use IsCollectionType;
    use CanTransformStrings;

    protected function validateEach($value) : void
    {
        if (! is_string($value)) {
            throw InvalidValue::invalidType($value, 'string');
        }
    }
}