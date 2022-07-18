<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\StringEnumType;

use FireMidge\Tests\ValueObject\Unit\Classes\StringEnumType;
use FireMidge\ValueObject\Exception\InvalidValue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringEnumType
 */
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
     */
    public function testFromStringWithValidValue(string $value) : void
    {
        $instance = StringEnumType::fromString($value);
        $this->assertSame($value, $instance->toString());
    }

    /**
     * @dataProvider validValueProvider
     */
    public function testFromStringOrNullWithValidValue(string $value) : void
    {
        $instance = StringEnumType::fromStringOrNull($value);
        $this->assertSame($value, $instance->toString());
    }

    /**
     * @dataProvider validValueProvider
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

    public function testFromStringOrNullWithNull() : void
    {
        $instance = StringEnumType::fromStringOrNull(null);
        $this->assertNull($instance);
    }

    /**
     * @dataProvider invalidValueProvider
     */
    public function testFromStringWithInvalidValue(string $value) : void
    {
        $this->expectException(InvalidValue::class);
        StringEnumType::fromString($value);
    }

    /**
     * @dataProvider invalidValueProvider
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
     */
    public function testFromStringOrNullWithInvalidValue(string $value) : void
    {
        $this->expectException(InvalidValue::class);
        StringEnumType::fromStringOrNull($value);
    }

    /**
     * @dataProvider invalidValueProvider
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