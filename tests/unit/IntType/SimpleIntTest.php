<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\IntType;

use FireMidge\Tests\ValueObject\Unit\Classes\SimpleIntType;
use FireMidge\ValueObject\Exception\InvalidValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(SimpleIntType::class)]
class SimpleIntTest extends TestCase
{
    public static function validValueProvider() : array
    {
        return [
            [ 0 ],
            [ 1 ],
            [ 700 ],
            [ 58760295 ],
        ];
    }

    #[DataProvider('validValueProvider')]
    public function testFromIntWithValidValue(int $value) : void
    {
        $instance = SimpleIntType::fromInt($value);
        $this->assertSame($value, $instance->toInt());
    }

    #[DataProvider('validValueProvider')]
    public function testFromIntOrNullWithValidValue(int $value) : void
    {
        $instance = SimpleIntType::fromIntOrNull($value);
        $this->assertSame($value, $instance->toInt());
    }

    public function testFromIntOrNullWithNull() : void
    {
        $instance = SimpleIntType::fromIntOrNull(null);
        $this->assertNull($instance);
    }

    public static function invalidValueProvider() : array
    {
        return [
            [ -1 ],
            [ -20 ],
        ];
    }

    #[DataProvider('invalidValueProvider')]
    public function testFromIntWithInvalidValue(int $value) : void
    {
        $this->expectException(InvalidValue::class);
        SimpleIntType::fromInt($value);
    }

    #[DataProvider('invalidValueProvider')]
    public function testFromIntWithInvalidValueErrorMessage(int $value) : void
    {
        $this->expectExceptionMessage(sprintf(
            'Value must be a positive number, value provided is %s.',
            (string) $value
        ));
        SimpleIntType::fromInt($value);
    }

    #[DataProvider('invalidValueProvider')]
    public function testFromIntOrNullWithInvalidValue(int $value) : void
    {
        $this->expectException(InvalidValue::class);
        SimpleIntType::fromIntOrNull($value);
    }

    #[DataProvider('invalidValueProvider')]
    public function testFromIntOrNullWithInvalidValueErrorMessage(int $value) : void
    {
        $this->expectExceptionMessage(sprintf(
            'Value must be a positive number, value provided is %s.',
            (string) $value
        ));
        SimpleIntType::fromIntOrNull($value);
    }

    public static function validStringValueProvider() : array
    {
        return [
            [ '0', 0 ],
            [ '1', 1 ],
            [ '700', 700 ],
            [ '58760295', 58760295 ],
        ];
    }

    #[DataProvider('validStringValueProvider')]
    public function testFromStringWithValidValue(string $input, int $output) : void
    {
        $instance = SimpleIntType::fromString($input);
        $this->assertSame($output, $instance->toInt());
    }

    #[DataProvider('validStringValueProvider')]
    public function testFromStringOrNullWithValidValue(string $input, int $output) : void
    {
        $instance = SimpleIntType::fromStringOrNull($input);
        $this->assertSame($output, $instance->toInt());
    }

    public function testFromStringOrNullWithNullValue() : void
    {
        $instance = SimpleIntType::fromStringOrNull(null);
        $this->assertSame(null, $instance);
    }

    public static function invalidStringValueProvider() : array
    {
        return [
            [ '', 'Value "" is invalid. (Value is not numeric.)' ],
            [ 'Hello1', 'Value "Hello1" is invalid. (Value is not numeric.)' ],
            [ '1Hello', 'Value "1Hello" is invalid. (Value is not numeric.)' ],
            [ '87e', 'Value "87e" is invalid. (Value is not numeric.)' ],
            [ '10.0', 'Value "10.0" is invalid. (Value is not an integer. Does not match expected "10".)' ],
            [ '10.5', 'Value "10.5" is invalid. (Value is not an integer. Does not match expected "10".)' ],
        ];
    }

    #[DataProvider('invalidStringValueProvider')]
    public function testFromStringWithInvalidValue(string $input, string $expectedMessage) : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($expectedMessage);

        SimpleIntType::fromString($input);
    }

    /**
     * This is a scenario where the class only has a min value but no max value.
     */
    public function testErrorMessageBelowMin() : void
    {
        $instance = SimpleIntType::fromInt(150);

        $this->expectExceptionMessage(
            'Cannot subtract value 151 from 150 as it brings the total (-1) below the minimum value of 0'
        );
        $instance->subtract(151);
    }

    public function testCanSubtractUntilMinValue() : void
    {
        $instance = SimpleIntType::fromInt(150);
        $result = $instance->subtract(150);

        $this->assertSame(0, $result->toInt());
    }

    public function testCanAddUntilMinValue() : void
    {
        $instance = SimpleIntType::fromInt(150);
        $result = $instance->add(-150);

        $this->assertSame(0, $result->toInt());
    }
}