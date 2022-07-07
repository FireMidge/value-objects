<?php
declare(strict_types=1);

namespace FireMidge\ValueObject;

use FireMidge\ValueObject\Exception\InvalidValue;

/**
 * A trait for value objects that consist of a string value
 * with custom validation rules.
 */
trait IsStringType
{
    private $value;

    private function __construct(string $value)
    {
        $value = $this->transform($value);
        $this->validate($value);
        $this->value = $value;
    }

    public static function fromString(string $value) : self
    {
        return new self($value);
    }

    public static function fromStringOrNull(?string $value = null) : ?self
    {
        if ($value === null) {
            return null;
        }

        return static::fromString($value);
    }

    public function toString() : string
    {
        return $this->value;
    }

    public function __toString() : string
    {
        return $this->value;
    }

    /**
     * Override this method to provide custom validation.
     *
     * @param string  $value  The input value to validate.
     *
     * @throws InvalidValue
     */
    protected function validate(string $value) : void
    {
        return;
    }

    /**
     * Override this method to do something to the string
     * before validating it,
     * e.g. trimming whitespace, lower-casing everything, ...
     *
     * @param string  $value  The input value to transform.
     *
     * @throws InvalidValue
     */
    protected function transform(string $value) : string
    {
        return $value;
    }

    /**
     * A convenience method you can call inside validate().
     */
    private function validateLength(string $value, ?int $minLength = null, ?int $maxLength = null) : void
    {
        if ($minLength === null && $maxLength === null) {
            return;
        }

        $length = mb_strlen($value);

        if ($minLength !== null && $maxLength !== null && ($length < $minLength || $length > $maxLength)) {
            throw InvalidValue::valueLengthNotBetween($value, $minLength, $maxLength);
        }
        else if ($minLength !== null && $length < $minLength) {
            throw InvalidValue::valueTooShort($value, $minLength);
        }
        else if ($maxLength !== null && $length > $maxLength) {
            throw InvalidValue::valueTooLong($value, $maxLength);
        }
    }

    /**
     * A convenience method you can call inside transform().
     */
    private function trimAndLowerCase(string $value) : string
    {
        return mb_strtolower(trim($value));
    }

    /**
     * A convenience method you can call inside transform().
     */
    private function trimAndUpperCase(string $value) : string
    {
        return mb_strtoupper(trim($value));
    }

    /**
     * A convenience method you can call inside transform().
     */
    private function trimAndCapitalise(string $value) : string
    {
        return ucfirst(mb_strtolower(trim($value)));
    }
}