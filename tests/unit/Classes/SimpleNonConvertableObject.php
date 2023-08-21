<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

class SimpleNonConvertableObject
{
    public function __construct(public string $value) {}
}