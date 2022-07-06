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
        $difference = array_diff($values, static::all());

        if (count($difference) > 0) {
            throw InvalidValue::valuesNotOfEnum(
                $values,
                static::all()
            );
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
        $self = $this->createClone();
        $self->values[] = $addedValue;

        return $self;
    }

    public function withoutValue($value) : self
    {
        $index = array_search($value, $this->values, true);
        if ($index === false) {
            throw ValueNotFound::inArray($value, $this->values);
        }

        $self = $this->createClone();
        unset($self->values[$index]);

        return $self;
    }

    public function contains($value) : bool
    {
        return (in_array($value, $this->values, true));
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

    private function createClone() : self
    {
        $self           = clone $this;
        $self->values   = array_merge([], $this->values); // Creating a shallow clone of the array

        return $self;
    }
}