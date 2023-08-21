<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

class SimpleTextObject
{
    public function __construct(public string $value) {}

    public function toText() : string
    {
        return $this->value;
    }
}