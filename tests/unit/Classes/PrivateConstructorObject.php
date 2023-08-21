<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

class PrivateConstructorObject
{
    private function __construct(private readonly mixed $value) {}

    public static function fromCustom(mixed $value) : static
    {
        return new static($value);
    }

    public function getCustom() : mixed
    {
        return $this->value;
    }
}