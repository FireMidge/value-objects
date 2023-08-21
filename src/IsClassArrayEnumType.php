<?php
declare(strict_types=1);

namespace FireMidge\ValueObject;

use FireMidge\ValueObject\Exception\InvalidValue;
use FireMidge\ValueObject\Helper\CanCreateInstance;
use RuntimeException;
use Throwable;

/**
 * A trait for a class that can hold an array of values (as opposed to a single value), and:
 * - where each array element has to be an instance of a specific value type class, and
 * - where the value type class only considers a limited set of values valid.
 */
trait IsClassArrayEnumType
{
    use IsArrayEnumType, CanCreateInstance;

    /**
     * Create a new array class from raw (non-target-class instance) values.
     * This method will try to convert each value inside the array to an object of the required class first,
     * before adding it to this array class.
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
     * Override to provide a custom list of valid class instances, or where there is no public static all()
     * method available on the target class.
     *
     * @return object[]
     */
    protected static function all() : array
    {
        if (! method_exists(static::className(), 'all') || ! is_callable([static::className(), 'all'])) {
            throw new RuntimeException(sprintf('Method %s is not implemented', __METHOD__));
        }

        try {
            $validValues = forward_static_call([static::className(), 'all']);
        } catch (Throwable $ex) {
            throw new RuntimeException(sprintf(
                'Method %s requires a custom implementation, which is not provided. %s',
                __METHOD__,
                $ex->getMessage()
            ), $ex->getCode(), $ex);
        }

        $all = [];
        foreach ($validValues as $value) {
            $all[] = static::convertIntoInstance($value, static::className());
        }
        return $all;
    }

    /**
     * Override this to gain more control over the conversion.
     *
     * @param mixed $value  The raw value to convert into the target class.
     *
     * @return object  An instance of the target class. Has to equal the class returned by static::className().
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
