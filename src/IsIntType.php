<?php
declare(strict_types=1);

namespace FireMidge\ValueObject;

use FireMidge\ValueObject\Exception\InvalidValue;

/**
 * A trait for value objects that consist of an integer value
 * with or without custom validation rules.
 *
 * Rules could be e.g. that an integer value that must be between certain values,
 * or an integer that must be odd/even, etc.
 */
trait IsIntType
{
    /**
     * @throws InvalidValue  If validation has been set up and $value is considered invalid.
     */
    private function __construct(private int $value)
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