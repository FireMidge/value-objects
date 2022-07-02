<?php
declare(strict_types=1);

namespace FireMidge\ValueObject;

use FireMidge\ValueObject\Exception\InvalidValue;

/**
 * A trait for value objects that consist of a string value
 * with custom validation rules.
 */
trait IsStringType
{
    private $value;

    private function __construct(string $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    public static function fromString(string $value) : self
    {
        return new self($value);
    }

    public function toString() : string
    {
        return $this->value;
    }

    public function __toString() : string
    {
        return $this->value;
    }

    /**
     * Override this method to provide custom validation.
     *
     * @param string  $value  The input value to validate.
     *
     * @throws InvalidValue
     */
    protected function validate(string $value) : void
    {
        return;
    }
}