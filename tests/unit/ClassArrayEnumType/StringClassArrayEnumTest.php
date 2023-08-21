<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\ClassArrayEnumType;

use FireMidge\Tests\ValueObject\Unit\Classes\StringClassArrayEnumType;
use FireMidge\Tests\ValueObject\Unit\Classes\StringEnumType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassArrayEnumType
 */
class StringClassArrayEnumTest extends TestCase
{
    public function testWithAll() : void
    {
        $this->assertEquals(StringClassArrayEnumType::fromArray([
            StringEnumType::spring(),
            StringEnumType::summer(),
            StringEnumType::autumn(),
            StringEnumType::winter(),
        ]), StringClassArrayEnumType::withAll());
    }

    public function testFromRawValues() : void
    {
        $this->assertEquals(StringClassArrayEnumType::fromArray([
            StringEnumType::spring(),
            StringEnumType::summer(),
            StringEnumType::winter(),
        ]), StringClassArrayEnumType::fromRawArray([
            'spring',
            'summer',
            'winter',
        ]));
    }
}
