<?php
declare(strict_types=1);

namespace FireMidge\ValueObject;

use FireMidge\ValueObject\Exception\InvalidValue;

/**
 * A trait for value objects that consist of a string value,
 * that must be one of a specified set of values.
 */
trait IsStringEnumType
{
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