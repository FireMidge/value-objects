<?php
declare(strict_types=1);

namespace FireMidge\ValueObject;

use FireMidge\ValueObject\Exception\ConversionError;
use FireMidge\ValueObject\Exception\InvalidValue;
use Throwable;

/**
 * A trait for creating a type where each value must be an instance of a class,
 * and there is no fixed set of valid values.
 */
trait IsClassCollectionType
{
    use IsCollectionType;

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

    protected function validateEach(mixed $value) : void
    {
        if (! is_object($value)) {
            throw InvalidValue::invalidType($value, 'object');
        }

        if (! is_a($value, static::className(), false)) {
            throw InvalidValue::notInstanceOf($value, static::className());
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
    abstract protected static function className() : string;

    /**
     * Override this to gain more control over the conversion.
     *
     * @param mixed $value  The raw value to convert into the target class.
     *
     * @return object  An instance of the target class. Has to equal the class returned by static::className().
     */
    protected static function convertFromRaw(mixed $value) : object
    {
        $className = static::className();
        if (is_string($value) && method_exists($className, 'fromString')) {
            return $className::fromString($value);
        }

        if (is_array($value) && method_exists($className, 'fromArray')) {
            return $className::fromArray($value);
        }

        if (is_int($value) && method_exists($className, 'fromInt')) {
            return $className::fromInt($value);
        }

        if (is_bool($value) && method_exists($className, 'fromBool')) {
            return $className::fromBool($value);
        }

        if (is_float($value) && method_exists($className, 'fromFloat')) {
            return $className::fromFloat($value);
        }

        if (is_float($value) && method_exists($className, 'fromDouble')) {
            return $className::fromDouble($value);
        }

        if ((is_float($value) || is_int($value)) && method_exists($className, 'fromNumber')) {
            return $className::fromNumber($value);
        }

        try {
            return new $className($value);
        } catch (Throwable) {
            throw ConversionError::couldNotConvert(
                $value,
                $className,
                sprintf('Override %s to customise conversion', __METHOD__)
            );
        }
    }
}