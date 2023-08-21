<?php
declare(strict_types=1);

namespace FireMidge\ValueObject;

use FireMidge\ValueObject\Exception\InvalidValue;
use FireMidge\ValueObject\Helper\CanExtractValueOfType;

/**
 * A trait for value objects that consist of an integer value
 * with or without custom validation rules.
 *
 * Rules could be e.g. that an integer value that must be between certain values,
 * or an integer that must be odd/even, etc.
 */
trait IsIntType
{
    use CanExtractValueOfType;

    /**
     * @throws InvalidValue  If validation has been set up and $value is considered invalid.
     */
    private function __construct(private readonly int $value)
    {
        $this->validate($value);
    }

    /**
     * Turns an integer into a new instance.
     *
     * @throws InvalidValue  If validation has been set up and $value is considered invalid.
     */
    public static function fromInt(int $value) : static
    {
        return new static($value);
    }

    /**
     * Accepts an integer as a string and returns a new instance.
     * Note that "1.0" is considered a float/double and causes an exception.
     *
     * @throws InvalidValue  If the value is not an integer value in a string.
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
     * Same as `fromInt`, but also accepts NULL values.
     * Returns NULL instead of a new instance if NULL is passed into it.
     *
     * Useful to be able to do e.g. `fromIntOrNull($request->get('status'));`
     * where you are not sure whether the value exists, and avoids having to
     * do a NULL-check before instantiating.
     *
     * @throws InvalidValue  If validation has been set up and $value is considered invalid.
     */
    public static function fromIntOrNull(?int $value = null) : ?static
    {
        if ($value === null) {
            return null;
        }

        return static::fromInt($value);
    }

    /**
     * Same as `fromString`, but also accepts NULL values.
     * Returns NULL instead of a new instance if NULL is passed into it.
     *
     * Useful to be able to do e.g. `fromStringOrNull($request->get('status'));`
     * where you are not sure whether the value exists, and avoids having to
     * do a NULL-check before instantiating.
     *
     * @throws InvalidValue  If validation has been set up and $value is considered invalid.
     */
    public static function fromStringOrNull(?string $value) : ?static
    {
        if ($value === null) {
            return null;
        }

        return static::fromString($value);
    }

    /**
     * Returns a new instance with the passed value added onto the value of the current instance.
     */
    public function add(int|float|object $valueToAdd) : static
    {
        return static::fromInt($this->toInt() + $this->getIntValueOfOther($valueToAdd));
    }

    /**
     * Returns a new instance with the passed value subtracted from the value of the current instance.
     */
    public function subtract(int|float|object $valueToSubtract) : static
    {
        return static::fromInt($this->toInt() - $this->getIntValueOfOther($valueToSubtract));
    }

    /**
     * Returns true if the value of the current instance is greater than the passed value.
     */
    public function isGreaterThan(int|float|object $other) : bool
    {
        return $this->toInt() > $this->getIntValueOfOther($other);
    }

    /**
     * Returns true if the value of the current instance is greater than or equal to the passed value.
     */
    public function isGreaterThanOrEqualTo(int|float|object $other) : bool
    {
        return $this->toInt() >= $this->getIntValueOfOther($other);
    }

    /**
     * Returns true if the value of the current instance is less than the passed value.
     */
    public function isLessThan(int|float|object $other) : bool
    {
        return $this->toInt() < $this->getIntValueOfOther($other);
    }

    /**
     * Returns true if the value of the current instance is less than or equal to the passed value.
     */
    public function isLessThanOrEqualTo(int|float|object $other) : bool
    {
        return $this->toInt() <= $this->getIntValueOfOther($other);
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

    public function __toString() : string
    {
        return (string) $this->value;
    }

    /**
     * Override this method to provide custom validation.
     *
     * @param int  $value  The input value to validate.
     *
     * @throws InvalidValue  If a min and/or max value have been set up and $value is not between them.
     */
    protected function validate(int $value) : void
    {
        if (static::minValidValue() !== null && $value < static::minValidValue()) {
            throw InvalidValue::valueTooLow($value, static::minValidValue());
        }

        if (static::maxValidValue() !== null && $value > static::maxValidValue()) {
            throw InvalidValue::valueTooHigh($value, static::maxValidValue());
        }
    }

    /**
     * Override this to set a different minimum valid value.
     * NULL means unlimited.
     */
    protected static function minValidValue() : ?int
    {
        return 0;
    }

    /**
     * Override this to set a different maximum valid value.
     * NULL means unlimited.
     */
    protected static function maxValidValue() : ?int
    {
        return null;
    }
}