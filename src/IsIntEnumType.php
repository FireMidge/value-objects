<?php
declare(strict_types=1);

namespace FireMidge\ValueObject;

use FireMidge\ValueObject\Exception\InvalidValue;

/**
 * A trait for value objects that consist of an integer value,
 * that must be one of a specified set of values.
 */
trait IsIntEnumType
{
    private $value;

    private function __construct(int $value)
    {
        if (! in_array($value, $this->all())) {
            throw InvalidValue::valueNotOneOfEnum(
                $value,
                $this->all()
            );
        }

        $this->value = $value;
    }

    public static function fromInt(int $value): self
    {
        return new self($value);
    }

    public function toInt(): int
    {
        return $this->value;
    }

    /**
     * Returns all allowed values.
     *
     * @return int[]
     */
    abstract protected function all(): array;
}