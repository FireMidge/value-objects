<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\ClassArrayEnumType;

use FireMidge\Tests\ValueObject\Unit\Classes\IntEnumType;
use FireMidge\Tests\ValueObject\Unit\Classes\StringClassArrayEnumType;
use FireMidge\Tests\ValueObject\Unit\Classes\StringEnumType;
use FireMidge\ValueObject\Exception\InvalidValue;
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

    public function testFromArrayWithNonObjectString() : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('Invalid value. Must be of type "object" but got "string"');

        StringClassArrayEnumType::fromArray(['invalid']);
    }

    public function testFromArrayWithNonObjectBoolean() : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('Invalid value. Must be of type "object" but got "boolean"');

        StringClassArrayEnumType::fromArray([true]);
    }

    public function testFromArrayWithObjectOfWrongClass() : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage(sprintf(
            'Invalid value. Must be an instance of "%s", but is "%s"',
            StringEnumType::class,
            IntEnumType::class
        ));

        StringClassArrayEnumType::fromArray([IntEnumType::fromInt(IntEnumType::AUTUMN)]);
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

    public function testFromRawValuesWithInvalidCallback() : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('Callback is returning the wrong type: Value "spring" is invalid. (Value is not numeric.)');

        StringClassArrayEnumType::fromRawArray(
            ['spring'],
            fn($rawValue) => IntEnumType::fromStringOrNull($rawValue)
        );
    }

    public function testFromRawValuesWithInvalidCallbackAndInvalidValue() : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('Callback is returning the wrong type: Value "invalidValue" is invalid. (Value is not numeric.)');

        StringClassArrayEnumType::fromRawArray(
            ['invalidValue'],
            fn($rawValue) => IntEnumType::fromStringOrNull($rawValue)
        );
    }

    public function testFromRawValuesWithInvalidValues() : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('Value "invalidValue" is invalid. Must be one of: "spring", "summer", "autumn", "winter"');

        StringClassArrayEnumType::fromRawArray(['invalidValue']);
    }
}
