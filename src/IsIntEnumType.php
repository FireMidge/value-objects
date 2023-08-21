<?php
declare(strict_types=1);

namespace FireMidge\ValueObject;

use FireMidge\ValueObject\Exception\InvalidValue;
use FireMidge\ValueObject\Helper\CanExtractValueOfType;

/**
 * A trait for value objects that consist of an integer value,
 * that must be one of a specified set of values.
 */
trait IsIntEnumType
{
    use CanExtractValueOfType;

    /**
     * @throws InvalidValue  If $value is not one of the allowed values.
     */
    private function __construct(private readonly int $value)
    {
        if (! in_array($value, $this->all())) {
            throw InvalidValue::valueNotOneOfEnum(
                $value,
                $this->all()
            );
        }
    }

    /**
     * Turns an integer into a new instance.
     *
     * @throws InvalidValue  If $value is not one of the allowed values.
     */
    public static function fromInt(int $value) : static
    {
        return new static($value);
    }

    /**
     * Same as `fromInt`, but also accepts NULL values.
     * Returns NULL instead of a new instance if NULL is passed into it.
     *
     * Useful to be able to do e.g. `fromIntOrNull($request->get('status'));`
     * where you are not sure whether the value exists, and avoids having to
     * do a NULL-check before instantiating.
     *
     * @throws InvalidValue  If $value is neither NULL nor one of the allowed values.
     */
    public static function fromIntOrNull(?int $value = null) : ?static
    {
        if ($value === null) {
            return null;
        }

        return static::fromInt($value);
    }

    /**
     * Accepts an integer as a string and returns a new instance.
     * Note that "1.0" is considered a float/double and causes an exception.
     *
     * @throws InvalidValue  If $value is not one of the allowed values.
     */
    public static function fromString(string $value) : static
    {
        if (! is_numeric($value)) {
            throw InvalidValue::invalidValue($value, 'Value is not numeric.');
        }

        $converted = (int) $value;
        if ((string) $converted !== $value) {
            throw InvalidValue::invalidValue($value, sprintf(
                'Value is not an integer. Does not match expected "%s".',
                $converted
            ));
        }

        return static::fromInt($converted);
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
     * If $other is an integer, this returns true if the values are equal.
     * If $other is a float, this returns true if the int-converted float equals the integer value of this instance.
     * If $other is an object, this returns true if the value returned by "toInt", "toFloat", "toDouble"
     * or "toNumber" can be converted into an integer, and equal the integer value of this instance.
     *
     * @param int|float|object|null $other        The value to compare to.
     * @param bool                  $strictCheck  If false, $other does not have to be of the same class.
     */
    public function isEqualTo(int|float|object|null $other, bool $strictCheck = true) : bool
    {
        if ($other === null) {
            return false;
        }

        if ($strictCheck && ! is_a($other, static::class)) {
            return false;
        }

        return $this->toInt() === $this->getIntValueOfOther($other);
    }

    /**
     * See isEqualTo for more details on the evaluation rules.
     *
     * @param int|float|object|null $other        The value to compare to.
     * @param bool                  $strictCheck  If false, $other does not have to be of the same class.
     */
    public function isNotEqualTo(int|float|object|null $other, bool $strictCheck = true) : bool
    {
        return ! $this->isEqualTo($other, $strictCheck);
    }

    /**
     * Converts the value object back into a scalar type.
     */
    public function toInt() : int
    {
        return $this->value;
    }

    /**
     * Required e.g. for object comparisons.
     */
    public function __toString() : string
    {
        return (string) $this->value;
    }

    /**
     * Returns all allowed values.
     *
     * @return int[]
     */
    abstract protected static function all() : array;
}