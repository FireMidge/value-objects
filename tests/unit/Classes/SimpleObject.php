<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

class SimpleObject
{
    public $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function __toString() : string
    {
        return $this->value;
    }
}