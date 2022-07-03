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
        static::validateValue($value, $this->allValidIntegers());
        $this->value = $value;
    }

    public static function fromInt(int $value) : self
    {
        return new self($value);
    }

    public static function fromString(string $value) : self
    {
        return new self(static::convertStringToInt($value, static::provideMap()));
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
     * The map from integer type to string type.
     * Provide the integer as the array key and the string as the array value.
     *
     * @return string[]  Key: integer; Value: string
     */
    abstract protected static function provideMap() : array;

    /**
     * Returns all allowed integer values.
     *
     * @return int[]
     */
    protected function allValidIntegers() : array
    {
        return array_keys($this->map);
    }

    /**
     * Returns all allowed string values.
     *
     * @return string[]
     */
    protected function allValidStrings() : array
    {
        return array_values($this->map);
    }

    /**
     * @throws InvalidValue
     */
    protected static function validateValue($value, array $validIntegers) : void
    {
        if (! in_array($value, $validIntegers, true)) {
            throw InvalidValue::valueNotOneOfEnum(
                $value,
                $validIntegers
            );
        }
    }

    protected static function convertStringToInt(string $value, array $map) : int
    {
        $result = array_search($value, $map, true);

        if ($result === false) {
            throw InvalidValue::valueNotOneOfEnum($value, array_values($map));
        }

        return $result;
    }
}