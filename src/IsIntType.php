<?php
declare(strict_types=1);

namespace FireMidge\ValueObject;

use FireMidge\ValueObject\Exception\InvalidValue;

/**
 * A trait for value objects that consist of an integer value
 * with custom validation rules.
 */
trait IsIntType
{
    private $value;

    private function __construct(int $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    public static function fromInt(int $value) : self
    {
        return new self($value);
    }

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
     * @throws InvalidValue
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