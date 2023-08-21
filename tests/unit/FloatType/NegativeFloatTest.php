<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\FloatType;

use FireMidge\Tests\ValueObject\Unit\Classes\NegativeFloatType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\NegativeFloatType
 */
class NegativeFloatTest extends TestCase
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
            [ -1 ],
            [ -20 ],
            [ -0.011 ],
            [ -69.68 ],
            [ -0.000000025 ],
        ];
    }

    /**
     * @dataProvider validValueProvider
     */
    public function testFromFloatWithValidValue(float $value) : void
    {
        $instance = NegativeFloatType::fromFloat($value);
        $this->assertSame($value, $instance->toFloat());
    }

    /**
     * @dataProvider validValueProvider
     */
    public function testFromFloatOrNullWithValidValue(float $value) : void
    {
        $instance = NegativeFloatType::fromFloatOrNull($value);
        $this->assertSame($value, $instance->toFloat());
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\NegativeFloatType::toFloat
     */
    public function testToFloatWithNegativeNumber() : void
    {
        $value    = -575.59;
        $instance = NegativeFloatType::fromFloat($value);
        $this->assertSame($value, $instance->toFloat());
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\NegativeFloatType::__toString
     */
    public function testMagicToStringWithNegativeNumber() : void
    {
        $value    = -575.5901;
        $instance = NegativeFloatType::fromFloat($value);
        $this->assertSame('-575.5901', (string) $instance);
    }

    public function testFromStringWithNegativeNumber() : void
    {
        $instance = NegativeFloatType::fromString( '-5850.44');
        $this->assertSame(-5850.44, $instance->toFloat());
    }

    public function testFromStringOrNullWithNegativeNumber() : void
    {
        $instance = NegativeFloatType::fromStringOrNull( '-5850.44');
        $this->assertSame(-5850.44, $instance->toFloat());
    }
}