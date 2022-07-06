<?php
declare(strict_types=1);

namespace FireMidge\ValueObject;

use FireMidge\ValueObject\Exception\InvalidValue;

/**
 * A trait for value objects that can be mapped between
 * an integer and a string type.
 */
trait IsIntStringMapType
{
    private $value;
    protected $map;

    private function __construct(int $value)
    {
        $this->map = static::provideMap();
        static::validateIntValue($value, $this->allValidIntegers());
        $this->value = $value;
    }

    public static function fromInt(int $value) : self
    {
        return new self($value);
    }

    public static function fromIntOrNull(?int $value) : ?self
    {
        if ($value === null) {
            return null;
        }

        return static::fromInt($value);
    }

    public static function fromString(string $value) : self
    {
        return new self(static::convertStringToInt($value, static::provideMap()));
    }

    public static function fromStringOrNull(?string $value) : ?self
    {
        if ($value === null) {
            return null;
        }

        return static::fromString($value);
    }

    public function toInt() : int
    {
        return $this->value;
    }

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
     * @throws InvalidValue
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
     * @throws InvalidValue
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