<?php
declare(strict_types=1);

namespace FireMidge\ValueObject;

use FireMidge\ValueObject\Exception\InvalidValue;

/**
 * A trait for value objects that consist of a string value,
 * that must be one of a specified set of values.
 */
trait IsStringEnumType
{
    private $value;

    private function __construct(string $value)
    {
        if (! in_array($value, $this->all())) {
            throw InvalidValue::valueNotOneOfEnum(
                $value,
                $this->all()
            );
        }

        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Returns all allowed values.
     *
     * @return string[]
     */
    abstract protected function all(): array;
}