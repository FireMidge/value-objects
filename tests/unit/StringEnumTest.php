<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit;

use FireMidge\Tests\ValueObject\Unit\classes\StringEnumType;
use FireMidge\ValueObject\Exception\InvalidValue;
use PHPUnit\Framework\TestCase;

class StringEnumTest extends TestCase
{
    public function validValueProvider() : array
    {
        return [
            [StringEnumType::SPRING ],
            [StringEnumType::SUMMER ],
            [StringEnumType::AUTUMN ],
            [StringEnumType::WINTER ],
        ];
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\classes\StringEnumType::fromString
     * @covers \FireMidge\Tests\ValueObject\Unit\classes\StringEnumType::toString
     */
    public function testFromStringWithValidValue(string $value) : void
    {
        $instance = StringEnumType::fromString($value);
        $this->assertSame($value, $instance->toString());
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\classes\StringEnumType::__toString
     * @covers \FireMidge\Tests\ValueObject\Unit\classes\StringEnumType::fromString
     */
    public function testMagicToString(string $value) : void
    {
        $instance = StringEnumType::fromString($value);
        $this->assertEquals($value, $instance);
    }

    public function invalidValueProvider() : array
    {
        return [
            [ '0' ],
            [ '1' ],
            [ 'invalid' ],
            [ 'summer1' ],
            [ '1summer' ],
            [ ' summer' ],
            [ 'SUMMER' ],
            [ 'summer ' ],
        ];
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\classes\StringEnumType::fromStringOrNull
     * @covers \FireMidge\Tests\ValueObject\Unit\classes\StringEnumType::toString
     */
    public function testFromStringOrNullWithValidValue(string $value) : void
    {
        $instance = StringEnumType::fromStringOrNull($value);
        $this->assertSame($value, $instance->toString());
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\classes\StringEnumType::fromStringOrNull
     */
    public function testFromStringOrNullWithNull() : void
    {
        $instance = StringEnumType::fromStringOrNull(null);
        $this->assertNull($instance);
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\classes\StringEnumType::fromString
     */
    public function testFromStringWithInvalidValue(string $value) : void
    {
        $this->expectException(InvalidValue::class);
        StringEnumType::fromString($value);
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\classes\StringEnumType::fromString
     */
    public function testFromStringWithInvalidValueErrorMessage(string $value) : void
    {
        $this->expectExceptionMessage(sprintf(
            'Value "%s" is invalid. Must be one of: "spring", "summer", "autumn", "winter"',
            $value
        ));
        StringEnumType::fromString($value);
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\classes\StringEnumType::fromStringOrNull
     */
    public function testFromStringOrNullWithInvalidValue(string $value) : void
    {
        $this->expectException(InvalidValue::class);
        StringEnumType::fromStringOrNull($value);
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\classes\StringEnumType::fromStringOrNull
     */
    public function testFromStringOrNullWithInvalidValueErrorMessage(string $value) : void
    {
        $this->expectExceptionMessage(sprintf(
            'Value "%s" is invalid. Must be one of: "spring", "summer", "autumn", "winter"',
            $value
        ));
        StringEnumType::fromStringOrNull($value);
    }
}