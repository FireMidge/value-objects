<?php
declare(strict_types=1);

namespace FireMidge\ValueObject;

use FireMidge\ValueObject\Exception\InvalidValue;
use FireMidge\ValueObject\Helper\CanCreateInstance;

/**
 * A trait for creating a type where each value must be an instance of a class,
 * and there is no fixed set of valid values.
 *
 * @template T of object
 * @extends IsCollectionType<T>
 */
trait IsClassCollectionType
{
    use IsCollectionType, CanCreateInstance;

    /**
     * Create a new class collection from raw (non-target-class instance) values.
     * This method will try to convert each value inside the array to an object of the required class first,
     * before adding it to this collection.
     *
     * @param array         $rawValues       An array of values which are not yet of the required class,
     *                                       but can be converted to it.
     * @param callable|null $customCallback  A custom callback accepting each array element as an argument,
     *                                       and which transforms the raw array element into an object of the
     *                                       target class.
     */
    public static function fromRawArray(array $rawValues, ?callable $customCallback = null) : static
    {
        $callback = $customCallback ?? fn($v) => static::convertFromRaw($v);

        try {
            return static::fromArray(array_map($callback, $rawValues));

        } catch (InvalidValue $ex) {
            // If a custom callback was passed, customise the error message to help the dev pinpoint the cause.
            if ($customCallback !== null) {
                throw new InvalidValue(
                    sprintf('Callback is returning the wrong type: %s', $ex->getMessage()),
                    $ex->getCode(),
                    $ex
                );
            }

            throw $ex;
        }
    }

    /**
     * Returns the FQN of the class that each value of this collection needs to be an instance of.
     * @returns class-string<T>
     */
    abstract protected static function className() : string;

    /**
     * Override this to allow duplicate values.
     */
    protected static function areValuesUnique() : bool
    {
        return true;
    }

    /**
     * Override this to gain more control over the conversion.
     *
     * @param mixed $value The raw value to convert into the target class.
     *
     * @return T An instance of the target class. Has to equal the class returned by static::className().
     */
    protected static function convertFromRaw(mixed $value) : object
    {
        return static::convertIntoInstance($value, static::className());
    }

    protected function validateEach(mixed $value) : void
    {
        if (! is_object($value)) {
            throw InvalidValue::invalidType($value, 'object');
        }

        if (! is_a($value, static::className(), false)) {
            throw InvalidValue::notInstanceOf($value, static::className());
        }
    }
}