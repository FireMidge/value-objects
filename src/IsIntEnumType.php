<?php
declare(strict_types=1);

namespace FireMidge\ValueObject;

use FireMidge\ValueObject\Exception\InvalidValue;

/**
 * A trait for value objects that consist of an integer value,
 * that must be one of a specified set of values.
 */
trait IsIntEnumType
{
    /**
     * @throws InvalidValue  If $value is not one of the allowed values.
     */
    private function __construct(private int $value)
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
     * Converts the value object back into a scalar type.
     */
    public function toInt() : int
    {
        return $this->value;
    }

    /**
     * Returns all allowed values.
     *
     * @return int[]
     */
    abstract protected static function all() : array;
}