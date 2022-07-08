<?php
declare(strict_types=1);

namespace FireMidge\ValueObject;

use FireMidge\ValueObject\Exception\InvalidValue;
use FireMidge\ValueObject\Exception\ValueNotFound;

/**
 * A trait for creating a type that contains an array of values,
 * where there is no list of valid values available,
 * e.g. a list of e-mail addresses.
 */
trait IsCollectionType
{
    private $values;

    /**
     * @throws InvalidValue  If values must be unique and $values contains duplicates.
     * @throws InvalidValue  If other validation checks have been set up and one or more of $values is invalid (e.g. invalid type).
     */
    private function __construct(array $values)
    {
        $values = array_map([$this, 'transformEach'], $values);
        array_map([$this, 'validateEach'], $values);

        if (static::areValuesUnique() && count(array_unique($values)) !== count($values)) {
            throw InvalidValue::containsDuplicates($values);
        }

        $this->values = $values;
    }

    /**
     * Creates a new instance from an array of values.
     */
    public static function fromArray(array $values) : self
    {
        return new static($values);
    }

    /**
     * Returns a new instance with $addedValue added to the list.
     *
     * @throws InvalidValue  If values must be unique and $addedValue is a duplicate.
     * @throws InvalidValue  If other validation checks have been set up and $value is invalid (e.g. invalid type).
     */
    public function withValue($addedValue) : self
    {
        if (static::areValuesUnique() && $this->contains($addedValue)) {
            throw InvalidValue::duplicateValue($addedValue, $this->values);
        }

        $newValues = array_merge($this->cloneValues($this->values), [ $addedValue ]);

        return new static($newValues);
    }

    /**
     * Returns a new instance without the value if the value previously existed.
     * If the value did not exist, it will return a new instance with the same
     * values.
     * Throws an exception if the value isn't valid and validation has been set up.
     *
     * @throws InvalidValue  If validation checks have been set up and $value is invalid.
     */
    public function withoutValue($value) : self
    {
        $this->validateEach($value);
        $newValues = $this->cloneValues($this->values);

        try {
            $index = $this->getIndexForValue($value);
            unset($newValues[$index]);
            $newValues = array_values($newValues); // Making sure it re-indexes.
        } catch (ValueNotFound $ex) {}

        return new static($newValues);
    }

    /**
     * Throws an exception when trying to remove a value that did not exist.
     *
     * @throws ValueNotFound If the value did not previously exist in the list.
     * @throws InvalidValue  If validation checks have been set up and $value is invalid.
     */
    public function tryWithoutValue($value) : self
    {
        $this->validateEach($value);
        $newValues = $this->cloneValues($this->values);
        $index     = $this->getIndexForValue($value);

        unset($newValues[$index]);
        $newValues = array_values($newValues); // Making sure it re-indexes.

        return new static($newValues);
    }

    /**
     * Returns true if this list contains $value.
     *
     * Note that checks are not performed with strict types, but if you set up a
     * type check in validateEach(), it will be performed before making the comparison.
     * You can also override this method to explicitly perform a strict comparison.
     */
    public function contains($value) : bool
    {
        $this->validateEach($value);
        return (in_array($value, $this->values));
    }

    /**
     * Converts this list back into a primitive array.
     */
    public function toArray() : array
    {
        return $this->values;
    }

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
     *
     * @throws InvalidValue  If the value is considered invalid.
     */
    protected function validateEach($value) : void
    {
        return;
    }

    /**
     * Override this method to do something to the string before validating it,
     * e.g. trimming whitespace, lower-casing everything, ...
     *
     * There are some convenience methods in `CanTransformStrings` that you can call, e.g.
     * - trimAndLowerCase
     * - trimAndUpperCase
     * - trimAndCapitalise
     *
     * @param mixed  $value  The input value to transform.
     *
     * @return mixed
     */
    protected function transformEach($value)
    {
        return $value;
    }

    private function cloneValues(array $values) : array
    {
        $clonedValues = [];
        foreach ($values as $value) {
            $clonedValues[] = (is_object($value))
                ? clone $value
                : $value;
        }
        return $clonedValues;
    }

    /**
     * Can be overridden to do a strict comparison if needed,
     * but `validateEach` is always called before calling
     * this method.
     */
    private function getIndexForValue($value) : int
    {
        // Not doing a strict comparison here by default,
        // as this would not work as intended for objects.

        $index = array_search($value, $this->values);
        if ($index === false) {
            throw ValueNotFound::inArray($value, $this->values);
        }

        return $index;
    }
}