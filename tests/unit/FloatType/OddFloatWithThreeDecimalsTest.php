<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\FloatType;

use FireMidge\Tests\ValueObject\Unit\Classes\OddFloatWithThreeDecimalsType;
use FireMidge\ValueObject\Exception\InvalidValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(OddFloatWithThreeDecimalsType::class)]
class OddFloatWithThreeDecimalsTest extends TestCase
{
    public static function validValueProvider() : array
    {
        return [
            [ 123 ],
            [ 567 ],
            [ 1 ],
            [ -3 ],
            [ -9999 ],
            [ 259 ],
        ];
    }

    #[DataProvider('validValueProvider')]
    public function testFromFloatWithValidValue(float $value) : void
    {
        $instance = OddFloatWithThreeDecimalsType::fromFloat($value);
        $this->assertSame($value, $instance->toFloat());
    }

    #[DataProvider('validValueProvider')]
    public function testFromFloatOrNullWithValidValue(float $value) : void
    {
        $instance = OddFloatWithThreeDecimalsType::fromFloatOrNull($value);
        $this->assertSame($value, $instance->toFloat());
    }

    public static function transformProvider() : array
    {
        return [
            [ 123.1234, 123.123 ],
            [ 567.7000001 , 567.7 ],
            [ 1.5555, 1.556 ],
            [ 1.5554, 1.555 ],
            [ -3.0001, -3.0 ],
            [ -3.5788, -3.579 ],
            [ -3.22, -3.22 ],
            [ -9999.9994, -9999.999 ],
        ];
    }

    #[DataProvider('transformProvider')]
    public function testTransform(float $input, float $output) : void
    {
        $instance = OddFloatWithThreeDecimalsType::fromFloat($input);
        $this->assertSame($output, $instance->toFloat());
    }

    public static function invalidValueProvider() : array
    {
        return [
            [ -2, 'Only odd values allowed. Value provided: "-2"' ],
            [ -20, 'Only odd values allowed. Value provided: "-20"' ],
            [ 122, 'Only odd values allowed. Value provided: "122"' ],
            [ 4, 'Only odd values allowed. Value provided: "4"' ],
            [ 568, 'Only odd values allowed. Value provided: "568"' ],
            [ 1000, 'Only odd values allowed. Value provided: "1000"' ],
            [ -9999.9999, 'Only odd values allowed. Value provided: "-10000"' ],
        ];
    }

    #[DataProvider('invalidValueProvider')]
    public function testFromFloatWithInvalidValue(float $value) : void
    {
        $this->expectException(InvalidValue::class);
        OddFloatWithThreeDecimalsType::fromFloat($value);
    }

    #[DataProvider('invalidValueProvider')]
    public function testFromFloatWithInvalidValueErrorMessage(float $value, string $expectedException) : void
    {
        $this->expectExceptionMessage($expectedException);
        OddFloatWithThreeDecimalsType::fromFloat($value);
    }

    #[DataProvider('invalidValueProvider')]
    public function testFromFloatOrNullWithInvalidValue(float $value) : void
    {
        $this->expectException(InvalidValue::class);
        OddFloatWithThreeDecimalsType::fromFloatOrNull($value);
    }

    #[DataProvider('invalidValueProvider')]
    public function testFromFloatOrNullWithInvalidValueErrorMessage(float $value, string $expectedException) : void
    {
        $this->expectExceptionMessage($expectedException);
        OddFloatWithThreeDecimalsType::fromFloatOrNull($value);
    }
}