<?php
declare(strict_types=1);

namespace FireMidge\ValueObject;

use FireMidge\ValueObject\Exception\InvalidValue;

/**
 * A trait for value objects that consist of a float value
 * with or without custom validation rules.
 *
 * Rules could be e.g. that a float value that must be between certain values,
 * or must be divisible by another number, ...
 */
trait IsFloatType
{
    private $value;

    /**
     * @throws InvalidValue  If validation has been set up and $value is considered invalid.
     */
    private function __construct(float $value)
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
    public static function fromFloat(float $value) : self
    {
        return new self($value);
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
    public static function fromFloatOrNull(?float $value = null) : ?self
    {
        if ($value === null) {
            return null;
        }

        return static::fromFloat($value);
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
     * e.g. limiting number of decimal points
     *
     * @param float  $value  The input value to transform.
     */
    protected function transform(float $value) : float
    {
        return $value;
    }
}