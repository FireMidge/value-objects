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
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\NegativeFloatType::fromFloat
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\NegativeFloatType::toFloat
     */
    public function testFromFloatWithValidValue(float $value) : void
    {
        $instance = NegativeFloatType::fromFloat($value);
        $this->assertSame($value, $instance->toFloat());
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\NegativeFloatType::fromFloatOrNull
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\NegativeFloatType::toFloat
     */
    public function testFromFloatOrNullWithValidValue(float $value) : void
    {
        $instance = NegativeFloatType::fromFloatOrNull($value);
        $this->assertSame((float) $value, $instance->toFloat());
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\NegativeFloatType::toFloat
     */
    public function testToFloatWithNegativeNumber() : void
    {
        $value    = -575.59;
        $instance = NegativeFloatType::fromFloat($value);
        $this->assertSame((float) $value, $instance->toFloat());
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
}