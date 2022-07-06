<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\IntStringMapType;

use FireMidge\Tests\ValueObject\Unit\Classes\IntStringMapType;
use FireMidge\ValueObject\Exception\InvalidValue;
use PHPUnit\Framework\TestCase;

class IntStringMapTest extends TestCase
{
    public function validValueProvider() : array
    {
        return [
            [ 1, 'spring' ],
            [ 2, 'summer' ],
            [ 3, 'autumn' ],
            [ 4, 'winter' ],
        ];
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntStringMapType::fromInt
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntStringMapType::toInt
     */
    public function testFromIntToIntWithValidValue(int $value) : void
    {
        $instance = IntStringMapType::fromInt($value);
        $this->assertSame($value, $instance->toInt());
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntStringMapType::fromInt
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntStringMapType::toString
     */
    public function testFromIntToStringWithValidValue(int $int, string $string) : void
    {
        $instance = IntStringMapType::fromInt($int);
        $this->assertSame($string, $instance->toString());
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntStringMapType::fromString
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntStringMapType::toString
     */
    public function testFromStringToStringWithValidValue(int $_, string $string) : void
    {
        $instance = IntStringMapType::fromString($string);
        $this->assertSame($string, $instance->toString());
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntStringMapType::fromString
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntStringMapType::toInt
     */
    public function testFromStringToIntWithValidValue(int $int, string $string) : void
    {
        $instance = IntStringMapType::fromString($string);
        $this->assertSame($int, $instance->toInt());
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntStringMapType::fromIntOrNull
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntStringMapType::toInt
     */
    public function testFromIntOrNullToIntWithValidValue(int $value) : void
    {
        $instance = IntStringMapType::fromIntOrNull($value);
        $this->assertSame($value, $instance->toInt());
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntStringMapType::fromIntOrNull
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntStringMapType::toString
     */
    public function testFromIntOrNullToStringWithValidValue(int $int, string $string) : void
    {
        $instance = IntStringMapType::fromIntOrNull($int);
        $this->assertSame($string, $instance->toString());
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntStringMapType::fromStringOrNull
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntStringMapType::toString
     */
    public function testFromStringOrNullToStringWithValidValue(int $_, string $string) : void
    {
        $instance = IntStringMapType::fromStringOrNull($string);
        $this->assertSame($string, $instance->toString());
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntStringMapType::fromStringOrNull
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntStringMapType::toInt
     */
    public function testFromStringOrNullToIntWithValidValue(int $int, string $string) : void
    {
        $instance = IntStringMapType::fromStringOrNull($string);
        $this->assertSame($int, $instance->toInt());
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntStringMapType::fromStringOrNull
     */
    public function testFromStringOrNullWithNull() : void
    {
        $instance = IntStringMapType::fromStringOrNull(null);
        $this->assertNull($instance);
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntStringMapType::fromIntOrNull
     */
    public function testFromIntOrNullWithNull() : void
    {
        $instance = IntStringMapType::fromIntOrNull(null);
        $this->assertNull($instance);
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntStringMapType::__toString
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntStringMapType::fromString
     */
    public function testMagicToString(int $_, string $string) : void
    {
        $instance = IntStringMapType::fromString($string);
        $this->assertSame($string, (string) $instance);
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntStringMapType::allValidIntegers
     */
    public function testAllValidIntegers() : void
    {
        $this->assertSame([ 1, 2, 3, 4 ], IntStringMapType::allValidIntegers());
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntStringMapType::allValidStrings
     */
    public function testAllValidStrings() : void
    {
        $this->assertSame([ 'spring', 'summer', 'autumn', 'winter' ], IntStringMapType::allValidStrings());
    }

    public function invalidIntValueProvider() : array
    {
        return [
            [ 0 ],
            [ 5 ],
            [ 10 ],
            [ -1 ],
            [ 200 ],
        ];
    }

    /**
     * @dataProvider invalidIntValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntStringMapType::fromIntOrNull
     */
    public function testFromIntOrNullWithInvalidValue(int $int) : void
    {
        $this->expectException(InvalidValue::class);
        IntStringMapType::fromIntOrNull($int);
    }

    /**
     * @dataProvider invalidIntValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntStringMapType::fromIntOrNull
     */
    public function testFromIntOrNullWithInvalidValueExceptionMessage(int $int) : void
    {
        $this->expectExceptionMessage(sprintf('Value "%d" is invalid. Must be one of: "1", "2", "3", "4"', $int));
        IntStringMapType::fromIntOrNull($int);
    }

    /**
     * @dataProvider invalidIntValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntStringMapType::fromInt
     */
    public function testFromIntWithInvalidValue(int $int) : void
    {
        $this->expectException(InvalidValue::class);
        IntStringMapType::fromInt($int);
    }

    /**
     * @dataProvider invalidIntValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntStringMapType::fromInt
     */
    public function testFromIntWithInvalidValueExceptionMessage(int $int) : void
    {
        $this->expectExceptionMessage(sprintf('Value "%d" is invalid. Must be one of: "1", "2", "3", "4"', $int));
        IntStringMapType::fromInt($int);
    }

    public function invalidStringValueProvider() : array
    {
        return [
            [ 's' ],
            [ 'summre' ],
            [ 'sprinG' ],
            [ 'SPRING' ],
            [ 'Spring' ],
            [ 'invalid' ],
            [ '' ],
            [ '-' ],
            [ '0' ],
            [ '1' ],
        ];
    }

    /**
     * @dataProvider invalidStringValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntStringMapType::fromStringOrNull
     */
    public function testFromStringOrNullWithInvalidValue(string $value) : void
    {
        $this->expectException(InvalidValue::class);
        IntStringMapType::fromStringOrNull($value);
    }

    /**
     * @dataProvider invalidStringValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntStringMapType::fromStringOrNull
     */
    public function testFromStringOrNullWithInvalidValueExceptionMessage(string $value) : void
    {
        $this->expectExceptionMessage(sprintf(
            'Value "%s" is invalid. Must be one of: "spring", "summer", "autumn", "winter"',
            $value
        ));
        IntStringMapType::fromStringOrNull($value);
    }

    /**
     * @dataProvider invalidStringValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntStringMapType::fromString
     */
    public function testFromStringWithInvalidValue(string $value) : void
    {
        $this->expectException(InvalidValue::class);
        IntStringMapType::fromString($value);
    }

    /**
     * @dataProvider invalidStringValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntStringMapType::fromString
     */
    public function testFromStringWithInvalidValueExceptionMessage(string $value) : void
    {
        $this->expectExceptionMessage(sprintf(
            'Value "%s" is invalid. Must be one of: "spring", "summer", "autumn", "winter"',
            $value
        ));
        IntStringMapType::fromString($value);
    }
}