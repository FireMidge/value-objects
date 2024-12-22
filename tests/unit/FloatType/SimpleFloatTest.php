<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\FloatType;

use FireMidge\Tests\ValueObject\Unit\Classes\SimpleFloatType;
use FireMidge\ValueObject\Exception\InvalidValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(SimpleFloatType::class)]
class SimpleFloatTest extends TestCase
{
    public static function validValueProvider() : array
    {
        return [
            [ 0 ],
            [ 1 ],
            [ 700 ],
            [ 700.1596 ],
            [ 58760295 ],
            [ 58760295.90097777 ],
            [ 7.5 ],
            [ 0.0001 ],
            [ 15.7860 ],
        ];
    }

    #[DataProvider('validValueProvider')]
    public function testFromFloatWithValidValue(float $value) : void
    {
        $instance = SimpleFloatType::fromFloat($value);
        $this->assertSame($value, $instance->toFloat());
    }

    #[DataProvider('validValueProvider')]
    public function testFromFloatOrNullWithValidValue(float $value) : void
    {
        $instance = SimpleFloatType::fromFloatOrNull($value);
        $this->assertSame($value, $instance->toFloat());
    }

    public static function validNumberProvider() : array
    {
        return array_merge(static::validValueProvider(), [
           [ 10 ],
           [ 255905 ],
        ]);
    }

    #[DataProvider('validNumberProvider')]
    public function testFromNumberWithValidValue(float|int $value) : void
    {
        $instance = SimpleFloatType::fromNumber($value);
        $this->assertSame((float) $value, $instance->toFloat());
    }

    #[DataProvider('validNumberProvider')]
    public function testFromNumberOrNullWithValidValue(float|int $value) : void
    {
        $instance = SimpleFloatType::fromNumberOrNull($value);
        $this->assertSame((float) $value, $instance->toFloat());
    }

    public function testFromFloatOrNullWithNull() : void
    {
        $instance = SimpleFloatType::fromFloatOrNull(null);
        $this->assertNull($instance);
    }

    #[DataProvider('validNumberProvider')]
    public function testFromNumberOrNullWithNull() : void
    {
        $instance = SimpleFloatType::fromNumberOrNull(null);
        $this->assertNull($instance);
    }

    public static function invalidValueProvider() : array
    {
        return [
            [ -1 ],
            [ -20 ],
            [ -0.011 ],
            [ -69.68 ],
            [ -0.000000025 ],
        ];
    }

    #[DataProvider('invalidValueProvider')]
    public function testFromFloatWithInvalidValue(float $value) : void
    {
        $this->expectException(InvalidValue::class);
        SimpleFloatType::fromFloat($value);
    }

    public static function invalidNumberProvider() : array
    {
        return array_merge(static::invalidValueProvider(), [
           [ -10 ],
           [ -1 ],
        ]);
    }

    #[DataProvider('invalidValueProvider')]
    public function testFromFloatWithInvalidValueErrorMessage(float $value) : void
    {
        $this->expectExceptionMessage(sprintf(
            'Value must be a positive number, value provided is %s.',
            (string) $value
        ));
        SimpleFloatType::fromFloat($value);
    }

    #[DataProvider('invalidNumberProvider')]
    public function testFromNumberWithInvalidValue(float|int $value) : void
    {
        $this->expectException(InvalidValue::class);
        SimpleFloatType::fromNumber($value);
    }

    #[DataProvider('invalidNumberProvider')]
    public function testFromNumberWithInvalidValueErrorMessage(float|int $value) : void
    {
        $this->expectExceptionMessage(sprintf(
            'Value must be a positive number, value provided is %s.',
            (string) $value
        ));
        SimpleFloatType::fromNumber($value);
    }

    #[DataProvider('invalidValueProvider')]
    public function testFromFloatOrNullWithInvalidValue(float $value) : void
    {
        $this->expectException(InvalidValue::class);
        SimpleFloatType::fromFloatOrNull($value);
    }

    #[DataProvider('invalidNumberProvider')]
    public function testFromNumberOrNullWithInvalidValue(float|int $value) : void
    {
        $this->expectException(InvalidValue::class);
        SimpleFloatType::fromNumberOrNull($value);
    }

    #[DataProvider('invalidValueProvider')]
    public function testFromFloatOrNullWithInvalidValueErrorMessage(float $value) : void
    {
        $this->expectExceptionMessage(sprintf(
            'Value must be a positive number, value provided is %s.',
            (string) $value
        ));
        SimpleFloatType::fromFloatOrNull($value);
    }

    public static function validStringValueProvider() : array
    {
        return [
            [ '0', 0.0 ],
            [ '1.59', 1.59 ],
            [ '1', 1.0 ],
            [ '700', 700.0 ],
            [ '700.8', 700.8 ],
            [ '58760.295', 58760.295 ],
            [ '58.760295', 58.760295 ],
        ];
    }

    #[DataProvider('validStringValueProvider')]
    public function testFromStringWithValidValue(string $input, float $output) : void
    {
        $instance = SimpleFloatType::fromString($input);
        $this->assertSame($output, $instance->toFloat());
    }

    #[DataProvider('validStringValueProvider')]
    public function testFromStringOrNullWithValidValue(string $input, float $output) : void
    {
        $instance = SimpleFloatType::fromStringOrNull($input);
        $this->assertSame($output, $instance->toFloat());
    }

    public function testFromStringOrNullWithNull() : void
    {
        $instance = SimpleFloatType::fromStringOrNull(null);
        $this->assertNull($instance);
    }

    public static function invalidStringValueProvider() : array
    {
        return [
            [ '', 'Value "" is invalid. (Value is not numeric.)' ],
            [ 'Hello1', 'Value "Hello1" is invalid. (Value is not numeric.)' ],
            [ '1Hello', 'Value "1Hello" is invalid. (Value is not numeric.)' ],
            [ '1 Hello', 'Value "1 Hello" is invalid. (Value is not numeric.)' ],
            [ '87e', 'Value "87e" is invalid. (Value is not numeric.)' ],
        ];
    }

    #[DataProvider('invalidStringValueProvider')]
    public function testFromStringWithInvalidValue(string $input, string $expectedMessage) : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($expectedMessage);

        SimpleFloatType::fromString($input);
    }

    /**
     * This is a scenario where the class only has a min value but no max value.
     */
    public function testErrorMessageBelowMin() : void
    {
        $instance = SimpleFloatType::fromFloat(150);

        $this->expectExceptionMessage(
            'Cannot subtract value 151 from 150 as it brings the total (-1) below the minimum value of 0'
        );
        $instance->subtract(151);
    }

    public function testCanSubtractUntilMinValue() : void
    {
        $instance = SimpleFloatType::fromNumber(150.5);
        $result = $instance->subtract(150.5);

        $this->assertSame(0.0, $result->toFloat());
    }

    public function testCanAddUntilMinValue() : void
    {
        $instance = SimpleFloatType::fromNumber(150.5);
        $result = $instance->add(-150.5);

        $this->assertSame(0.0, $result->toFloat());
    }
}