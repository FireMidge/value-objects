<?php
declare(strict_types=1);

namespace FireMidge\ValueObject;

use FireMidge\ValueObject\Exception\InvalidValue;
use FireMidge\ValueObject\Exception\ValueNotFound;

// TODO: Create IsStringArrayEnumType and IsIntArrayEnumType.
trait IsArrayEnumType
{
    private $values;

    private function __construct(array $values)
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

        $this->values = $values;
    }

    public static function fromArray(array $values) : self
    {
        return new static($values);
    }

    public static function withAll() : self
    {
        return new static(static::all());
    }

    public function withValue($addedValue) : self
    {
        if (static::areValuesUnique() && $this->contains($addedValue)) {
            throw InvalidValue::duplicateValue($addedValue, $this->values);
        }

        $newValues = array_merge($this->cloneValues($this->values), [ $addedValue ]);

        return new static($newValues);
    }

    public function withoutValue($value) : self
    {
        $this->validateEach($value);
        $newValues = $this->cloneValues($this->values);
        $index     = $this->getIndexForValue($value);

        unset($newValues[$index]);
        $newValues = array_values($newValues); // Making sure it re-indexes.

        return new static($newValues);
    }

    public function contains($value) : bool
    {
        $this->validateEach($value);
        return (in_array($value, $this->values));
    }

    public function toArray() : array
    {
        return $this->values;
    }

    /**
     * Returns all allowed values.
     *
     * @return mixed[]
     */
    abstract protected static function all() : array;

    /**
     * Override this and return true if you want to disallow adding the same
     * value more than once.
     */
    protected static function areValuesUnique() : bool
    {
        return false;
    }

    /**
     * Override this to provide some validation to each element.
     */
    protected function validateEach($value) : void
    {
        return;
    }

    private function cloneValues(array $values) : array
    {
        $clonedValues = [];
        foreach ($values as $value) {
            $clonedValues[] = clone $value;
        }
        return $clonedValues;
    }

    private function getIndexForValue($value) : int
    {
        $index = array_search($value, $this->values);
        if ($index === false) {
            throw ValueNotFound::inArray($value, $this->values);
        }

        return $index;
    }
}