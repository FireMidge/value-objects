<?php
declare(strict_types=1);

namespace FireMidge\ValueObject;

use FireMidge\ValueObject\Exception\InvalidValue;
use FireMidge\ValueObject\Helper\CanExtractValueOfType;

/**
 * A trait for value objects that consist of a float value
 * with or without custom validation rules.
 *
 * Rules could be e.g. that a float value that must be between certain values,
 * or must be divisible by another number, ...
 */
trait IsFloatType
{
    use CanExtractValueOfType;

    /**
     * @throws InvalidValue  If validation has been set up and $value is considered invalid.
     */
    private function __construct(private float $value)
    {
        $value = $this->transform($value);
        $this->validate($value);
        $this->value = $value;
    }

    /**
     * Turns a float into a new instance.
     *
     * @throws InvalidValue  If validation has been set up and $value is considered invalid.
     */
    public static function fromFloat(float $value) : static
    {
        return new static($value);
    }

    /**
     * Same as `fromFloat`, but also accepts NULL values.
     * Returns NULL instead of a new instance if NULL is passed into it.
     *
     * Useful to be able to do e.g. `fromFloatOrNull($request->get('weight'));`
     * where you are not sure whether the value exists, and avoids having to
     * do a NULL-check before instantiating.
     *
     * @throws InvalidValue  If validation has been set up and $value is considered invalid.
     */
    public static function fromFloatOrNull(?float $value = null) : ?static
    {
        if ($value === null) {
            return null;
        }

        return static::fromFloat($value);
    }

    /**
     * Accepts a float (or integer) as a string and returns a new instance.
     *
     * @throws InvalidValue  If the value is not a float value in a string.
     */
    public static function fromString(string $value) : static
    {
        if (! is_numeric($value)) {
            throw InvalidValue::invalidValue($value, 'Value is not numeric.');
        }

        return static::fromFloat((float) $value);
    }

    /**
     * Same as `fromString`, but also accepts NULL values.
     * Returns NULL instead of a new instance if NULL is passed into it.
     *
     * Useful to be able to do e.g. `fromStringOrNull($request->get('score'));`
     * where you are not sure whether the value exists, and avoids having to
     * do a NULL-check before instantiating.
     *
     * @throws InvalidValue  If validation has been set up and $value is considered invalid.
     */
    public static function fromStringOrNull(?string $value = null) : ?static
    {
        if ($value === null) {
            return null;
        }

        return static::fromString($value);
    }

    /**
     * Turns a number (whether float or integer) into a new instance.
     * Use this if you're not sure whether the number is typed as a float or an integer.
     *
     * @throws InvalidValue  If validation has been set up and $value is considered invalid.
     */
    public static function fromNumber(float|int $value) : static
    {
        return new static((float) $value);
    }

    /**
     * Same as `fromNumber`, but also accepts NULL values.
     * Returns NULL instead of a new instance if NULL is passed into it.
     *
     * Useful to be able to do e.g. `fromNumberOrNull($request->get('weight'));`
     * where you are not sure whether the value exists, and avoids having to
     * do a NULL-check before instantiating.
     *
     * @throws InvalidValue  If validation has been set up and $value is considered invalid.
     */
    public static function fromNumberOrNull(float|int|null $value) : ?static
    {
        if ($value === null) {
            return null;
        }

        return static::fromNumber($value);
    }

    /**
     * Returns a new instance with the passed value added onto the value of the current instance.
     */
    public function add(float|int|object $valueToAdd) : static
    {
        return static::fromFloat($this->toFloat() + $this->getFloatValueOfOther($valueToAdd));
    }

    /**
     * Returns a new instance with the passed value subtracted from the value of the current instance.
     */
    public function subtract(float|int|object $valueToSubtract) : static
    {
        return static::fromFloat($this->toFloat() - $this->getFloatValueOfOther($valueToSubtract));
    }

    /**
     * Returns true if the value of the current instance is greater than the passed value.
     */
    public function isGreaterThan(float|int|object $other) : bool
    {
        return $this->toFloat() > $this->getFloatValueOfOther($other);
    }

    /**
     * Returns true if the value of the current instance is greater than or equal to the passed value.
     */
    public function isGreaterThanOrEqualTo(float|int|object $other) : bool
    {
        return $this->toFloat() >= $this->getFloatValueOfOther($other);
    }

    /**
     * Returns true if the value of the current instance is less than the passed value.
     */
    public function isLessThan(float|int|object $other) : bool
    {
        return $this->toFloat() < $this->getFloatValueOfOther($other);
    }

    /**
     * Returns true if the value of the current instance is less than or equal to the passed value.
     */
    public function isLessThanOrEqualTo(float|int|object $other) : bool
    {
        return $this->toFloat() <= $this->getFloatValueOfOther($other);
    }

    /**
     * If $strictCheck is true, this only returns true if $other is an object of the same class
     * AND has the same value.
     *
     * If $strictCheck is false, see rules below:
     *
     * If $other is a float, this returns true if the values are equal.
     * If $other is an integer, this returns true if the float-converted integer equals the float value of this instance.
     * If $other is an object, this returns true if the value returned by "toFloat", "toDouble", "toInt"
     * or "toNumber" can be converted into a float, and equal the float value of this instance.
     *
     * @param null|float|int|object $other        The value to compare to.
     * @param bool                  $strictCheck  If false, $other does not have to be of the same class.
     */
    public function isEqualTo(null|float|int|object $other, bool $strictCheck = true) : bool
    {
        if ($other === null) {
            return false;
        }

        if ($strictCheck && ! is_a($other, static::class)) {
            return false;
        }

        return $this->toFloat() === $this->getFloatValueOfOther($other);
    }

    /**
     * See isEqualTo for more details on the evaluation rules.
     *
     * @param float|int|object|null $other        The value to compare to.
     * @param bool                  $strictCheck  If false, $other does not have to be of the same class.
     */
    public function isNotEqualTo(float|int|object|null $other, bool $strictCheck = true) : bool
    {
        return ! $this->isEqualTo($other, $strictCheck);
    }

    /**
     * Converts the value object back into a scalar type.
     */
    public function toFloat() : float
    {
        return $this->value;
    }

    public function __toString() : string
    {
        return (string) $this->value;
    }

    /**
     * Override this method to provide custom validation.
     *int
     * @param float  $value  The input value to validate.
     *
     * @throws InvalidValue  If a min and/or max value have been set up and $value is not between them.
     */
    protected function validate(float $value) : void
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
    protected static function minValidValue() : ?float
    {
        return 0;
    }

    /**
     * Override this to set a different maximum valid value.
     * NULL means unlimited.
     */
    protected static function maxValidValue() : ?float
    {
        return null;
    }

    /**
     * Override this method to do something to the float before validating it,
     * e.g. limiting number of decimal points.
     *
     * @param float  $value  The input value to transform.
     */
    protected function transform(float $value) : float
    {
        return $value;
    }
}