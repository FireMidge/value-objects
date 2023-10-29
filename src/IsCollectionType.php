<?php
declare(strict_types=1);

namespace FireMidge\ValueObject;

use FireMidge\ValueObject\Exception\DuplicateValue;
use FireMidge\ValueObject\Exception\InvalidValue;
use FireMidge\ValueObject\Exception\ValueNotFound;

/**
 * A trait for creating a type that contains an array of values,
 * where there is no list of valid values available,
 * e.g. a list of e-mail addresses.
 */
trait IsCollectionType
{
    /**
     * @throws InvalidValue  If values must be unique and $values contains duplicates.
     * @throws InvalidValue  If other validation checks have been set up and one or more of $values is invalid (e.g. invalid type).
     */
    private function __construct(private array $values)
    {
        $values = array_map([$this, 'transformEach'], $values);
        array_map([$this, 'validateEach'], $values);

        if ((static::areValuesUnique() || static::ignoreDuplicateValues())
            && count(array_unique($values)) !== count($values)
        ) {
            $values = $this->handleDuplicateValues($values);
        }

        $this->values = $values;
    }

    /**
     * Creates a new instance from an array of values.
     */
    public static function fromArray(array $values) : static
    {
        return new static($values);
    }

    /**
     * Creates a new instance from an empty array.
     */
    public static function empty() : static
    {
        return new static([]);
    }

    /**
     * Returns a new instance with $addedValue added to the list.
     *
     * @throws InvalidValue  If values must be unique and $addedValue is a duplicate.
     * @throws InvalidValue  If other validation checks have been set up and $addedValue is invalid (e.g. invalid type).
     */
    public function withValue($addedValue) : static
    {
        $addedValue = $this->transformEach($addedValue);

        if ((static::areValuesUnique() || static::ignoreDuplicateValues()) && $this->contains($addedValue)) {
            $this->handleDuplicateValue($addedValue);
            return new static($this->values);
        }

        $newValues = array_merge($this->cloneValues($this->values), [ $addedValue ]);

        return new static($newValues);
    }

    /**
     * Returns a new instance with all of $addedValues added to the list.
     *
     * @throws InvalidValue  If values must be unique and any of $addedValues is a duplicate.
     * @throws InvalidValue  If other validation checks have been set up and any of $addedValues is invalid (e.g. invalid type).
     */
    public function withValues(array $addedValues) : static
    {
        $instance = $this;
        foreach ($addedValues as $addedValue) {
            $instance = $instance->withValue($addedValue);
        }

        return $instance;
    }

    /**
     * Returns a new instance without the value if the value previously existed.
     * If the value did not exist, it will return a new instance with the same
     * values.
     *
     * @throws InvalidValue  If validation checks have been set up and $value is invalid.
     */
    public function withoutValue(mixed $value) : static
    {
        $this->validateEach($value);
        $newValues = $this->cloneValues($this->values);

        try {
            $index = $this->getIndexForValue($value);
            unset($newValues[$index]);
            $newValues = array_values($newValues); // Making sure it re-indexes.
        } catch (ValueNotFound) {}

        return new static($newValues);
    }

    /**
     * Returns a new instance without any of the values in $values.
     * Any that did not previously exist are ignored.
     *
     * @throws InvalidValue  If validation checks have been set up and any of $values is invalid.
     */
    public function withoutValues(array $values) : static
    {
        $instance = $this;
        foreach ($values as $valueToBeRemoved) {
            $instance = $instance->withoutValue($valueToBeRemoved);
        }

        return $instance;
    }


    /**
     * Same as `withoutValue` but throws an exception when trying to
     * remove a value that did not exist.
     *
     * @throws ValueNotFound If the value did not previously exist in the list.
     * @throws InvalidValue  If validation checks have been set up and $value is invalid.
     */
    public function tryWithoutValue($value) : static
    {
        $this->validateEach($value);
        $newValues = $this->cloneValues($this->values);
        $index     = $this->getIndexForValue($value);

        unset($newValues[$index]);
        $newValues = array_values($newValues); // Making sure it re-indexes.

        return new static($newValues);
    }

    /**
     * Same as `withoutValues` but throws an exception when trying to
     * remove a value that did not exist.
     *
     * @throws ValueNotFound If any of the values did not previously exist in the list.
     * @throws InvalidValue  If validation checks have been set up and any of $values is invalid.
     */
    public function tryWithoutValues(array $values) : static
    {
        $instance = $this;
        foreach ($values as $valueToBeRemoved) {
            $instance = $instance->tryWithoutValue($valueToBeRemoved);
        }

        return $instance;
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
     * Finds a value within this collection based on a callback.
     * Returns the value matching the criteria within the callback, or `null` if no match was found.
     *
     * @param callable $searchCallback A callback that will be applied to each element in the array,
     *                                 until the callback returns `true` for the matching element.
     *                                 E.g. fn($v) => $v.name === 'Susan'
     *                                 or: fn($v, $k) => $k === 10
     *
     * @return mixed  The element matching the criteria, or `null` if no match was found.
     */
    public function find(callable $searchCallback) : mixed
    {
        foreach ($this->values as $k => $value) {
            if ($searchCallback($value, $k) === true) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Finds the index of a value within this collection based on a callback.
     * Returns the index matching the criteria within the callback, or `null` if no match was found.
     *
     * @param callable $searchCallback A callback that will be applied to each element in the array,
     *                                 until the callback returns `true` for the matching element.
     *                                 E.g. fn($v) => $v.name === 'Susan'
     *                                 or: fn($v, $k) => $k === 10
     *
     * @return string|int|null  The element matching the criteria, or `null` if no match was found.
     */
    public function findIndex(callable $searchCallback) : string|int|null
    {
        foreach ($this->values as $k => $value) {
            if ($searchCallback($value, $k) === true) {
                return $k;
            }
        }

        return null;
    }

    public function current() : mixed
    {
        return current($this->values);
    }

    /**
     * Converts this list back into a primitive array.
     */
    public function toArray() : array
    {
        return $this->values;
    }

    /**
     * Returns the number of elements in the collection.
     */
    public function count() : int
    {
        return count($this->values);
    }

    /**
     * Whether the collection contains any elements.
     */
    public function isEmpty() : bool
    {
        return count($this->values) === 0;
    }

    /**
     * Whether the collection does not contain any elements.
     */
    public function isNotEmpty() : bool
    {
        return ! $this->isEmpty();
    }

    /**
     * If $strictCheck is true, this only returns true if $other is an object of the same class
     * AND has the same values.
     *
     * If $strictCheck is false, see rules below:
     *
     * If $other is an array, this returns true if the arrays (of this and $other) have the same *string* values.
     *                        This means, each item is converted to a string before comparing.
     *                        The order of the elements does not matter.
     * If $other is an object, and the object has a "toArray" method, the object is converted to an array this way
     *                         and the arrays (of this and $other->toArray()) compared as described above.
     * If $other is an object and has no "toArray" method, the object's public properties are converted into an array
     *                        and then compared like the array comparison described above.
     *
     * @param null|array|object $other        The value to compare to.
     * @param bool              $strictCheck  If false, $other does not have to be of the same class.
     */
    public function isEqualTo(null|array|object $other = null, bool $strictCheck = true) : bool
    {
        if ($other === null) {
            return false;
        }

        if ($strictCheck && ! is_a($other, static::class)) {
            return false;
        }

        if (is_array($other)) {
            return $this->isEqualToArray($other);
        }

        return $this->isEqualToObject($other);
    }

    /**
     * See isEqualTo for more details on the evaluation rules.
     *
     * @param null|array|object $other        The value to compare to.
     * @param bool              $strictCheck  If false, $other does not have to be of the same class.
     */
    public function isNotEqualTo(null|array|object $other = null, bool $strictCheck = true) : bool
    {
        return ! $this->isEqualTo($other, $strictCheck);
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
     * Override this and return true if you want to silently ignore
     * duplicate values.
     * This means all values exist only once in the collection.
     */
    protected static function ignoreDuplicateValues() : bool
    {
        return false;
    }

    /**
     * Override this to provide some validation to each element.
     *
     * @throws InvalidValue  If the value is considered invalid.
     */
    protected function validateEach(mixed $value) : void
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

    private function isEqualToArray(array $other) : bool
    {
        if (count($other) !== count($this->values)) {
            return false;
        }

        if (count(array_intersect($this->values, $other)) !== count($this->values)) {
            return false;
        }

        return true;
    }

    private function isEqualToObject(object $other) : bool
    {
        if (method_exists($other, 'toArray')) {
            return $this->isEqualToArray($other->toArray());
        }

        return $this->isEqualToArray((array) $other);
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

        $index = array_search($value, $this->values, false);
        if ($index === false) {
            throw ValueNotFound::inArray($value, $this->values);
        }

        return $index;
    }

    private function handleDuplicateValues(array $values) : array
    {
        // @codeCoverageIgnoreStart
        //  This is never going to be executed because the same check also happens in __construct (for performance reasons).
        // But I don't want to remove it from here, just in case. It's an easy check.
        if (! static::areValuesUnique() && (! static::ignoreDuplicateValues())) {
            return $values;
        }
        // @codeCoverageIgnoreEnd

        if (static::areValuesUnique() && (! static::ignoreDuplicateValues())) {
            throw DuplicateValue::containsDuplicates($values);
        }

        return array_unique($values);
    }

    private function handleDuplicateValue($value) : void
    {
        if (static::areValuesUnique() && (! static::ignoreDuplicateValues())) {
            throw DuplicateValue::duplicateValue($value, $this->values);
        }
    }
}