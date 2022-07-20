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
     * If $strictCheck is true, this only returns true if $other is an object of the same class
     * AND has the same string and integer values.
     *
     * If $strictCheck is false, see rules below:
     *
     * If $other is an integer, this returns true if the integer value of this is equal to $other.
     * If $other is a string, this returns true if the string value of this is equal to $other.
     * If $other is an object:
     * - If it has both a toString and a toInt method, this returns true only if both the integer and the string
     *   value match.
     * - If it has only a toInt method, this returns true if the integer values match.
     * - If it only has a toString method, this returns true if the string values match.
     *
     * @param object|string|int|null $other        The value to compare to.
     * @param bool                   $strictCheck  If false, $other does not have to be of the same class.
     */
    public function isEqualTo(null|object|string|int $other = null, bool $strictCheck = true) : bool
    {
        if ($other === null) {
            return false;
        }

        if (! $strictCheck) {
            return $this->looseComparison($other);
        }

        if (! is_a($other, static::class)) {
            return false;
        }

        return $this->compareObject($other);
    }

    /**
     * See isEqualTo for more details on the evaluation rules.
     *
     * @param object|string|int|null $other        The value to compare to.
     * @param bool                   $strictCheck  If false, $other does not have to be of the same class.
     */
    public function isNotEqualTo(null|object|string|int $other = null, bool $strictCheck = true) : bool
    {
        return ! $this->isEqualTo($other, $strictCheck);
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

    private function looseComparison(object|string|int $other) : bool
    {
        if (is_string($other) && $this->toString() === $other) {
            return true;

        } else if (is_int($other) && $this->toInt() === $other) {
            return true;

        } else if (is_object($other)) {
            return $this->compareObject($other);
        }

        return false;
    }

    private function compareObject(object $other) : bool
    {
        $hasIntMethod    = method_exists($other, 'toInt');
        $hasStringMethod = method_exists($other, 'toString');

        if ($hasIntMethod && $hasStringMethod) {
            return $other->toInt() === $this->toInt() && $other->toString() === $this->toString();
        }

        if ($hasIntMethod) {
            return $other->toInt() === $this->toInt();
        }

        if ($hasStringMethod) {
            return $other->toString() === $this->toString();
        }

        return false;
    }
}