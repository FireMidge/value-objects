<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\FloatType;

use FireMidge\Tests\ValueObject\Unit\Classes\OddFloatWithThreeDecimalsType;
use FireMidge\ValueObject\Exception\InvalidValue;
use PHPUnit\Framework\TestCase;

class OddFloatWithThreeDecimalsTest extends TestCase
{
    public function validValueProvider(): array
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

    /**
     * @dataProvider validValueProvider
     *
     * @covers       \FireMidge\Tests\ValueObject\Unit\Classes\OddFloatWithThreeDecimalsType::fromFloat
     * @covers       \FireMidge\Tests\ValueObject\Unit\Classes\OddFloatWithThreeDecimalsType::toFloat
     */
    public function testFromFloatWithValidValue(float $value): void
    {
        $instance = OddFloatWithThreeDecimalsType::fromFloat($value);
        $this->assertSame($value, $instance->toFloat());
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers       \FireMidge\Tests\ValueObject\Unit\Classes\OddFloatWithThreeDecimalsType::fromFloatOrNull
     * @covers       \FireMidge\Tests\ValueObject\Unit\Classes\OddFloatWithThreeDecimalsType::toFloat
     */
    public function testFromFloatOrNullWithValidValue(float $value): void
    {
        $instance = OddFloatWithThreeDecimalsType::fromFloatOrNull($value);
        $this->assertSame($value, $instance->toFloat());
    }

    public function transformProvider(): array
    {
        return [
            [ 123.1234, 123.123 ],
            [ 567.7000001 , 567.7 ],
            [ 1.5555, 1.556 ],
            [ 1.5554, 1.555 ],
            [ -3.0001, -3 ],
            [ -3.5788, -3.579 ],
            [ -3.22, -3.22 ],
            [ -9999.9994, -9999.999 ],
        ];
    }

    /**
     * @dataProvider transformProvider
     *
     * @covers       \FireMidge\Tests\ValueObject\Unit\Classes\OddFloatWithThreeDecimalsType::fromFloat
     * @covers       \FireMidge\Tests\ValueObject\Unit\Classes\OddFloatWithThreeDecimalsType::toFloat
     */
    public function testTransform(float $input, float $output): void
    {
        $instance = OddFloatWithThreeDecimalsType::fromFloat($input);
        $this->assertSame($output, $instance->toFloat());
    }

    public function invalidValueProvider() : array
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

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\OddFloatWithThreeDecimalsType::fromFloat
     */
    public function testFromFloatWithInvalidValue(float $value) : void
    {
        $this->expectException(InvalidValue::class);
        OddFloatWithThreeDecimalsType::fromFloat($value);
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\OddFloatWithThreeDecimalsType::fromFloat
     */
    public function testFromFloatWithInvalidValueErrorMessage(float $value, string $expectedException) : void
    {
        $this->expectExceptionMessage($expectedException);
        OddFloatWithThreeDecimalsType::fromFloat($value);
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\OddFloatWithThreeDecimalsType::fromFloatOrNull
     */
    public function testFromFloatOrNullWithInvalidValue(float $value) : void
    {
        $this->expectException(InvalidValue::class);
        OddFloatWithThreeDecimalsType::fromFloatOrNull($value);
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\OddFloatWithThreeDecimalsType::fromFloatOrNull
     */
    public function testFromFloatOrNullWithInvalidValueErrorMessage(float $value, string $expectedException) : void
    {
        $this->expectExceptionMessage($expectedException);
        OddFloatWithThreeDecimalsType::fromFloatOrNull($value);
    }
}