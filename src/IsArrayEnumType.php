<?php
declare(strict_types=1);

namespace FireMidge\ValueObject;

use FireMidge\ValueObject\Exception\InvalidValue;

trait IsArrayEnumType
{
    use IsCollectionType;

    /**
     * @throws InvalidValue  If one or more of $values is not one of the allowed values.
     * @throws InvalidValue  If values must be unique and $values contains duplicates.
     * @throws InvalidValue  If other validation checks have been set up and one or more of $values is invalid (e.g. invalid type).
     */
    private function __construct(private array $values)
    {
        array_map([$this, 'validateEach'], $values);

        $difference = array_diff($values, static::all());

        if (count($difference) > 0) {
            throw InvalidValue::valuesNotOfEnum(
                $difference,
                static::all()
            );
        }

        if (static::areValuesUnique() && count(array_unique($values)) !== count($values)) {
            throw InvalidValue::containsDuplicates($values);
        }
    }

    /**
     * Returns a new instance with all available values.
     */
    public static function withAll() : static
    {
        return new static(static::all());
    }

    /**
     * Returns all allowed values.
     *
     * @return mixed[]
     */
    abstract protected static function all() : array;
}