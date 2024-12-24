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
 *
 * You can implement \Iterator in your class.
 *
 * @template T of mixed
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
     *
     * @param T[] $values
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
     * @param T $addedValue
     *
     * @throws InvalidValue  If values must be unique and $addedValue is a duplicate.
     * @throws InvalidValue  If other validation checks have been set up and $addedValue is invalid (e.g. invalid type).
     */
    public function withValue(mixed $addedValue) : static
    {
        $addedValue = $this->transformEach($addedValue);

        if ((static::areValuesUnique() || static::ignoreDuplicateValues()) && $this->contains($addedValue)) {
            return new static($this->handleDuplicateValue($addedValue));
        }

        $newValues = array_merge($this->cloneValues($this->values), [ $addedValue ]);

        return new static($newValues);
    }

    /**
     * Returns a new instance with all of $addedValues added to the list.
     *
     * @param T[] $addedValues
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
     * @param T $value
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
     * @param T[] $values
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
     * @param T $value
     *
     * @throws ValueNotFound If the value did not previously exist in the list.
     * @throws InvalidValue  If validation checks have been set up and $value is invalid.
     */
    public function tryWithoutValue(mixed $value) : static
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
     * @param T[] $values
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
     * @param T $value
     *
     * Note that checks are not performed with strict types, but if you set up a
     * type check in validateEach(), it will be performed before making the comparison.
     * You can also override this method to explicitly perform a strict comparison.
     */
    public function contains(mixed $value) : bool
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
     * @return ?T  The element matching the criteria, or `null` if no match was found.
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

    /**
     * Merges $other into itself.
     * This modifies the current instance.
     *
     * @param object|T[]|array $other  Can be an instance of the same class, an instance of a different class
     *                                 holding the same types of value, or an array.
     */
    public function merge(object|array $other) : void
    {
        $new = $this->withValues($this->convertToArray($other));
        $this->values = $new->values;
    }

    /**
     * Returns a new instance which contains the values of both this instance and $other.
     *
     * @param object|T[]|mixed[] $other Can be an instance of the same class, an instance of
     *                                  a different class holding the same types of value, or an array.
     */
    public function withMerged(object|array $other) : static
    {
        return $this->withValues($this->convertToArray($other));
    }

    /**
     * Removes the last element from itself and returns it.
     * This modifies the current instance.
     *
     * @return ?T
     */
    public function pop() : mixed
    {
        return array_pop($this->values);
    }

    /**
     * Similar to split(), with 2 key differences:
     * 1) Instead of specifying the number of elements to keep, this lets
     *    you specify the number of elements to remove from the end of the array.
     * 2) The elements are returned in reverse order.
     *    Removing the last 2 elements from 'a', 'b', 'c' results in 'c', 'b'.
     *
     * This modifies the current instance.
     *
     * @param int $numberOfElements How many elements to remove from the end of the array.
     *                              The removed elements will be returned in a new instance
     *                              in reverse order.
     */
    public function popMultiple(int $numberOfElements) : static
    {
        $poppedCards = static::empty();
        for ($i = 0; $i < $numberOfElements; $i++) {
            $poppedElement = $this->pop();

            if ($poppedElement === null) {
                break; // Breaking for performance reasons, as we already know all future pops won't work either
            }

            $poppedCards = $poppedCards->withValue($poppedElement);
        }

        return $poppedCards;
    }

    /**
     * Similar to popMultiple(), with 2 key differences:
     *  1) Instead of specifying the number of elements to remove, this lets
     *     you specify the number of elements to keep in the original array.
     *     Note that you can force this by using a negative $numberOfValuesToKeep.
     *  2) The elements are returned in the same order they were in the original array.
     *     Removing the last 2 elements from 'a', 'b', 'c' results in 'b', 'c'.
     *
     * This modifies the current instance.
     *
     * @param int $numberOfValuesToKeep  How many elements should remain in the current instance.
     *                                   The removed elements are returned in a new instance.
     *                                   If this is a negative number, it changes to the number
     *                                   of elements to remove.
     */
    public function split(int $numberOfValuesToKeep) : static
    {
        $numberOfValuesToRemove = $numberOfValuesToKeep < 0
            ? abs($numberOfValuesToKeep)
            : count($this->values) - $numberOfValuesToKeep;

        return $this->popMultiple($numberOfValuesToRemove)->withReversedOrder();
    }

    /**
     * Returns a new instance, where all the values are in the reversed order.
     *
     * If the current instance has ['a', 'b', 'c'], the new instance will
     * have ['c', 'b', 'a'].
     */
    public function withReversedOrder() : static
    {
        $values = $this->values;
        return static::fromArray(array_reverse($values));
    }

    /**
     * Returns the first element contained within itself.
     * This moves the pointer of the current instance but does not remove any elements.
     *
     * @return ?T
     */
    public function first() : mixed
    {
        reset($this->values);
        return $this->current();
    }

    /**
     * Returns the first element contained within itself.
     * This moves the pointer of the current instance but does not remove any elements.
     *
     * @return ?T
     */
    public function last() : mixed
    {
        $lastElement = end($this->values);

        if ($lastElement === false) {
            return null;
        }

        return $lastElement;
    }

    /**
     * @return ?T
     */
    public function current() : mixed
    {
        $item = current($this->values);

        if ($item === false) {
            return null;
        }

        return $item;
    }


    public function next() : void
    {
        next($this->values);
    }

    public function previous() : void
    {
        prev($this->values);
    }

    public function key() : mixed
    {
        return key($this->values);
    }

    public function valid() : bool
    {
        return $this->current() !== null;
    }

    public function rewind() : void
    {
        reset($this->values);
    }

    public function shuffle() : void
    {
        shuffle($this->values);
    }

    /**
     * Converts this list back into a primitive array.
     *
     * @return T[]
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
     * This allows you to implement the \JsonSerializable interface.
     */
    public function jsonSerialize() : array
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
     * @return T
     */
    protected function transformEach(mixed $value) : mixed
    {
        return $value;
    }

    /**
     * Override this to provide custom handling of duplicate values within an array.
     *
     * @param T[] $values The list of values to be added to the instance, which contain duplicates.
     *
     * @throws DuplicateValue If there is/are duplicate value(s) that cannot be accepted.
     */
    protected function handleDuplicateValues(array $values) : array
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

    /**
     * Override this to provide custom handling of adding a value that is a duplicate of an
     * already existing value.
     *
     * @param T $value The new value to be added, which is a duplicate of one of the
     *                     pre-existing values.
     *
     * @return array The full array of values to be stored against the object. You will have handled
     *               the duplicate value by this point and either added it to the list
     *               (maybe in a transformed state) or thrown an exception.
     *
     * @throws DuplicateValue If the duplicate value cannot be accepted.
     */
    protected function handleDuplicateValue(mixed $value) : array
    {
        if (static::areValuesUnique() && (! static::ignoreDuplicateValues())) {
            throw DuplicateValue::duplicateValue($value, $this->values);
        }

        return $this->values;
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
        return $this->isEqualToArray($this->convertToArray($other));
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
    private function getIndexForValue(mixed $value) : int
    {
        // Not doing a strict comparison here by default,
        // as this would not work as intended for objects.

        $index = array_search($value, $this->values, false);
        if ($index === false) {
            throw ValueNotFound::inArray($value, $this->values);
        }

        return $index;
    }

    private function convertToArray(object|array $other) : array
    {
        if (is_array($other)) {
            return $other;
        }

        if (method_exists($other, 'toArray')) {
            return $other->toArray();
        }

        return (array) $other;
    }
}