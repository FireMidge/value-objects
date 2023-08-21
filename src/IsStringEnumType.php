<?php
declare(strict_types=1);

namespace FireMidge\ValueObject;

use FireMidge\ValueObject\Exception\InvalidValue;
use FireMidge\ValueObject\Helper\CanExtractValueOfType;

/**
 * A trait for value objects that consist of a string value,
 * that must be one of a specified set of values.
 */
trait IsStringEnumType
{
    use CanExtractValueOfType;

    /**
     * @throws InvalidValue  If $value is not one of the allowed values.
     */
    private function __construct(private string $value)
    {
        $value = $this->transform($value);

        if (! in_array($value, static::all())) {
            throw InvalidValue::valueNotOneOfEnum(
                $value,
                $this->all()
            );
        }

        $this->value = $value;
    }

    /**
     * Turns a string into a new instance.
     *
     * @throws InvalidValue  If $value is not one of the allowed values.
     */
    public static function fromString(string $value) : static
    {
        return new static($value);
    }

    /**
     * Same as `fromString`, but also accepts NULL values.
     * Returns NULL instead of a new instance if NULL is passed into it.
     *
     * Useful to be able to do e.g. `fromStringOrNull($request->get('status'));`
     * where you are not sure whether the value exists, and avoids having to
     * do a NULL-check before instantiating.
     *
     * @throws InvalidValue  If $value is neither NULL nor one of the allowed values.
     */
    public static function fromStringOrNull(?string $value = null) : ?static
    {
        if ($value === null) {
            return null;
        }

        return static::fromString($value);
    }

    /**
     * If $strictCheck is true, this only returns true if $other is an object of the same class
     * AND has the same value.
     *
     * If $strictCheck is false, see rules below:
     *
     * If $other is a string, this returns true if $other equals the string value of this instance.
     * If $other is an object, this returns true if the value returned by "toString", "toText" or
     * the magic "__toString" equals the string value of this instance.
     *
     * @param string|object|null $other        The value to compare to.
     * @param bool               $strictCheck  If false, $other does not have to be of the same class.
     */
    public function isEqualTo(string|object|null $other, bool $strictCheck = true) : bool
    {
        if ($other === null) {
            return false;
        }

        if ($strictCheck && ! is_a($other, static::class)) {
            return false;
        }

        return $this->value === $this->getStringValueOfOther($other);
    }

    /**
     * See isEqualTo for more details on the evaluation rules.
     *
     * @param string|object|null $other        The value to compare to.
     * @param bool               $strictCheck  If false, $other does not have to be of the same class.
     */
    public function isNotEqualTo(string|object|null $other, bool $strictCheck = true) : bool
    {
        return ! $this->isEqualTo($other, $strictCheck);
    }

    /**
     * Converts the object back to a scalar type.
     */
    public function toString() : string
    {
        return $this->value;
    }

    public function __toString() : string
    {
        return $this->value;
    }

    /**
     * Returns all allowed values.
     *
     * @return string[]
     */
    abstract protected static function all() : array;

    /**
     * Override this method to do something to the string before validating it,
     * e.g. trimming whitespace, lower-casing everything, ...
     *
     * There are some convenience methods already here that you can call, e.g.
     * - trimAndLowerCase
     * - trimAndUpperCase
     * - trimAndCapitalise
     *
     * @param string  $value  The input value to transform.
     */
    protected function transform(string $value) : string
    {
        return $value;
    }
}