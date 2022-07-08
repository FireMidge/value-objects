<?php
declare(strict_types=1);

namespace FireMidge\ValueObject;

use FireMidge\ValueObject\Exception\InvalidValue;

/**
 * A trait for value objects that can be mapped between
 * an integer and a string type.
 *
 * This may be useful when you e.g. store a value in the database as an integer (for faster indexing),
 * but convert it to a string for a public API (for better readability).
 */
trait IsIntStringMapType
{
    protected $map;

    /**
     * @throws InvalidValue  If $value is not one of the allowed values.
     */
    private function __construct(private int $value)
    {
        $this->map = static::provideMap();
        static::validateIntValue($value, $this->allValidIntegers());
    }

    /**
     * Creates a new instance from an integer.
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
    public static function fromIntOrNull(?int $value) : ?static
    {
        if ($value === null) {
            return null;
        }

        return static::fromInt($value);
    }

    /**
     * Creates a new instance from a string.
     * It converts the string to the equivalent integer based on provideMap().
     *
     * @throws InvalidValue  If $value is not one of the allowed values.
     */
    public static function fromString(string $value) : static
    {
        return new static(static::convertStringToInt($value, static::provideMap()));
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
    public static function fromStringOrNull(?string $value) : ?static
    {
        if ($value === null) {
            return null;
        }

        return static::fromString($value);
    }

    /**
     * Converts the object to the integer value.
     */
    public function toInt() : int
    {
        return $this->value;
    }

    /**
     * Converts the object to the string equivalent.
     */
    public function toString() : string
    {
        return $this->map[$this->value];
    }

    public function __toString() : string
    {
        return $this->toString();
    }

    /**
     * Returns all allowed integer values.
     *
     * @return int[]
     */
    public static function allValidIntegers() : array
    {
        return array_keys(static::provideMap());
    }

    /**
     * Returns all allowed string values.
     *
     * @return string[]
     */
    public static function allValidStrings() : array
    {
        return array_values(static::provideMap());
    }

    /**
     * The map from integer type to string type.
     * Provide the integer as the array key and the string as the array value.
     *
     * @return string[]  Key: integer; Value: string
     */
    abstract protected static function provideMap() : array;

    /**
     * @throws InvalidValue  If $value is not one of the allowed values.
     */
    protected static function validateIntValue(int $value, array $validIntegers) : void
    {
        if (! in_array($value, $validIntegers, true)) {
            throw InvalidValue::valueNotOneOfEnum(
                $value,
                $validIntegers
            );
        }
    }

    /**
     * @throws InvalidValue  If $value is not one of the available values in $map.
     */
    protected static function convertStringToInt(string $value, array $map) : int
    {
        $result = array_search($value, $map, true);

        if ($result === false) {
            throw InvalidValue::valueNotOneOfEnum($value, array_values($map));
        }

        return $result;
    }
}