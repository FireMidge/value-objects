<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\FloatType;

use FireMidge\Tests\ValueObject\Unit\Classes\SimpleFloatType;
use FireMidge\ValueObject\Exception\InvalidValue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\SimpleFloatType
 */
class SimpleFloatTest extends TestCase
{
    public function validValueProvider() : array
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

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\SimpleFloatType::fromFloat
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\SimpleFloatType::toFloat
     */
    public function testFromFloatWithValidValue(float $value) : void
    {
        $instance = SimpleFloatType::fromFloat($value);
        $this->assertSame($value, $instance->toFloat());
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\SimpleFloatType::fromFloatOrNull
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\SimpleFloatType::toFloat
     */
    public function testFromFloatOrNullWithValidValue(float $value) : void
    {
        $instance = SimpleFloatType::fromFloatOrNull($value);
        $this->assertSame($value, $instance->toFloat());
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\SimpleFloatType::fromFloatOrNull
     */
    public function testFromFloatOrNullWithNull() : void
    {
        $instance = SimpleFloatType::fromFloatOrNull(null);
        $this->assertNull($instance);
    }

    public function invalidValueProvider() : array
    {
        return [
            [ -1 ],
            [ -20 ],
            [ -0.011 ],
            [ -69.68 ],
            [ -0.000000025 ],
        ];
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\SimpleFloatType::fromFloat
     */
    public function testFromFloatWithInvalidValue(float $value) : void
    {
        $this->expectException(InvalidValue::class);
        SimpleFloatType::fromFloat($value);
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\SimpleFloatType::fromFloat
     */
    public function testFromFloatWithInvalidValueErrorMessage(float $value) : void
    {
        $this->expectExceptionMessage(sprintf(
            'Value must be a positive number, value provided is %s.',
            (string) $value
        ));
        SimpleFloatType::fromFloat($value);
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\SimpleFloatType::fromFloatOrNull
     */
    public function testFromFloatOrNullWithInvalidValue(float $value) : void
    {
        $this->expectException(InvalidValue::class);
        SimpleFloatType::fromFloatOrNull($value);
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\SimpleFloatType::fromFloatOrNull
     */
    public function testFromFloatOrNullWithInvalidValueErrorMessage(float $value) : void
    {
        $this->expectExceptionMessage(sprintf(
            'Value must be a positive number, value provided is %s.',
            (string) $value
        ));
        SimpleFloatType::fromFloatOrNull($value);
    }
}