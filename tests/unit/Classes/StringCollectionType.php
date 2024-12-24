<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\Exception\InvalidValue;
use FireMidge\ValueObject\Helper\CanTransformStrings;
use FireMidge\ValueObject\IsCollectionType;

/**
 * @extends IsCollectionType<string>
 */
class StringCollectionType
{
    use IsCollectionType;
    use CanTransformStrings;

    protected function validateEach(mixed $value) : void
    {
        if (! is_string($value)) {
            throw InvalidValue::invalidType($value, 'string');
        }
    }

    protected function transformEach(mixed $value) : mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        return $this->trimAndCapitalise($value);
    }

    protected static function areValuesUnique() : bool
    {
        return true;
    }
}