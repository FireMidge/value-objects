<?php
declare(strict_types=1);

namespace FireMidge\ValueObject;

use FireMidge\ValueObject\Exception\InvalidValue;

/**
 * A trait for creating a type where each value must be an instance of a class,
 * and there is no fixed set of valid values.
 */
trait IsClassCollectionType
{
    use IsCollectionType;

    protected function validateEach($value) : void
    {
        if (! is_object($value)) {
            throw InvalidValue::invalidType($value, 'object');
        }

        if (! is_a($value, $this->className(), false)) {
            throw InvalidValue::notInstanceOf($value, $this->className());
        }
    }

    /**
     * Override this to allow duplicate values.
     */
    protected static function areValuesUnique() : bool
    {
        return true;
    }

    /**
     * Returns the FQN of the class that each value of this collection needs to be an instance of.
     */
    abstract protected function className() : string;
}