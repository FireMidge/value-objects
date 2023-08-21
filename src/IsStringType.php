<?php
declare(strict_types=1);

namespace FireMidge\ValueObject;

use FireMidge\ValueObject\Exception\InvalidValue;
use FireMidge\ValueObject\Helper\CanExtractValueOfType;
use FireMidge\ValueObject\Helper\CanTransformStrings;
use LogicException;

/**
 * A trait for value objects that consist of a string value
 * with or without custom validation rules.
 *
 * This type is useful for e.g. storing a name
 * and other string values that don't have a finite list of allowed values
 * or where it is not feasible to list all allowed values.
 */
trait IsStringType
{
    use CanTransformStrings, CanExtractValueOfType;

    /**
     * @throws InvalidValue  If validation has been set up and $value is considered invalid.
     */
    private function __construct(private string $value)
    {
        $value = $this->transform($value);
        $this->validate($value);
        $this->value = $value;
    }

    /**
     * Turns a string into a new instance.
     *
     * @throws InvalidValue  If validation has been set up and $value is considered invalid.
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
     * Override this method to provide custom validation.
     *
     * There are convenience methods already here that you can call, e.g.:
     * - validateLength
     * - validateEmailAddress
     *
     * @param string  $value  The input value to validate.
     *
     * @throws InvalidValue  If you consider the value invalid.
     */
    protected function validate(string $value) : void
    {
        return;
    }

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

    /**
     * A convenience method you can call inside validate().
     */
    private function validateLength(string $value, ?int $minLength = null, ?int $maxLength = null) : void
    {
        if ($minLength === null && $maxLength === null) {
            return;
        }

        if ($minLength !== null && $maxLength !== null && $minLength > $maxLength) {
            throw new LogicException(sprintf(
                'Trying to validate length of a string, '
                . 'but the required $minLength "%d" is higher than $maxLength "%d"',
                $minLength,
                $maxLength
            ));
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
     * A convenience method you can call inside validate().
     */
    private function validateEmailAddress(string $value) : void
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            throw InvalidValue::notAnEmailAddress($value);
        }
    }
}