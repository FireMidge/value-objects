<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\IntEnumType;

use FireMidge\Tests\ValueObject\Unit\Classes\IntEnumType;
use FireMidge\ValueObject\Exception\InvalidValue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntEnumType
 */
class IntEnumTest extends TestCase
{
    public function validValueProvider() : array
    {
        return [
            [IntEnumType::SPRING ],
            [IntEnumType::SUMMER ],
            [IntEnumType::AUTUMN ],
            [IntEnumType::WINTER ],
        ];
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntEnumType::fromInt
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntEnumType::toInt
     */
    public function testFromIntWithValidValue(int $value) : void
    {
        $instance = IntEnumType::fromInt($value);
        $this->assertSame($value, $instance->toInt());
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntEnumType::fromIntOrNull
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntEnumType::toInt
     */
    public function testFromIntOrNullWithValidValue(int $value) : void
    {
        $instance = IntEnumType::fromIntOrNull($value);
        $this->assertSame($value, $instance->toInt());
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntEnumType::fromIntOrNull
     */
    public function testFromIntOrNullWithNull() : void
    {
        $instance = IntEnumType::fromIntOrNull(null);
        $this->assertNull($instance);
    }

    public function invalidValueProvider() : array
    {
        return [
            [ 0 ],
            [ 5 ],
            [ -1 ],
        ];
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntEnumType::fromInt
     */
    public function testFromIntWithInvalidValue(int $value) : void
    {
        $this->expectException(InvalidValue::class);
        IntEnumType::fromInt($value);
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntEnumType::fromInt
     */
    public function testFromIntWithInvalidValueErrorMessage(int $value) : void
    {
        $this->expectExceptionMessage(sprintf(
            'Value "%s" is invalid. Must be one of: "1", "2", "3", "4"',
            $value
        ));
        IntEnumType::fromInt($value);
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntEnumType::fromIntOrNull
     */
    public function testFromIntOrNullWithInvalidValue(int $value) : void
    {
        $this->expectException(InvalidValue::class);
        IntEnumType::fromIntOrNull($value);
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntEnumType::fromIntOrNull
     */
    public function testFromIntOrNullWithInvalidValueErrorMessage(int $value) : void
    {
        $this->expectExceptionMessage(sprintf(
            'Value "%s" is invalid. Must be one of: "1", "2", "3", "4"',
            $value
        ));
        IntEnumType::fromIntOrNull($value);
    }

    public function validStringValueProvider() : array
    {
        return [
            [ '1', 1 ],
            [ '2', 2 ],
            [ '3', 3 ],
            [ '4', 4 ],
        ];
    }

    /**
     * @dataProvider validStringValueProvider
     */
    public function testFromStringWithValidValue(string $input, int $output) : void
    {
        $instance = IntEnumType::fromString($input);
        $this->assertSame($output, $instance->toInt());
    }

    /**
     * @dataProvider validStringValueProvider
     */
    public function testFromStringOrNullWithValidValue(string $input, int $output) : void
    {
        $instance = IntEnumType::fromStringOrNull($input);
        $this->assertSame($output, $instance->toInt());
    }

    public function testFromStringOrNullWithNullValue() : void
    {
        $instance = IntEnumType::fromStringOrNull(null);
        $this->assertSame(null, $instance);
    }

    /**
     * @dataProvider validStringValueProvider
     */
    public function testMagicToString(string $expectedOutput, int $input) : void
    {
        $instance = IntEnumType::fromInt($input);
        $this->assertSame($expectedOutput, (string) $instance);
    }

    public function invalidStringValueProvider() : array
    {
        return [
            [ '', 'Value "" is invalid. (Value is not numeric.)' ],
            [ 'Hello1', 'Value "Hello1" is invalid. (Value is not numeric.)' ],
            [ '1Hello', 'Value "1Hello" is invalid. (Value is not numeric.)' ],
            [ '87e', 'Value "87e" is invalid. (Value is not numeric.)' ],
            [ '10.0', 'Value "10.0" is invalid. (Value is not an integer. Does not match expected "10".)' ],
            [ '10.5', 'Value "10.5" is invalid. (Value is not an integer. Does not match expected "10".)' ],
            [ '5', 'Value "5" is invalid. Must be one of: "1", "2", "3", "4"' ],
            [ '0', 'Value "0" is invalid. Must be one of: "1", "2", "3", "4"' ],
        ];
    }

    /**
     * @dataProvider invalidStringValueProvider
     */
    public function testFromStringWithInvalidValue(string $input, string $expectedMessage) : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($expectedMessage);

        IntEnumType::fromString($input);
    }
}