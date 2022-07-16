<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\IntType;

use FireMidge\Tests\ValueObject\Unit\Classes\SimpleIntType;
use FireMidge\ValueObject\Exception\InvalidValue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\SimpleIntType
 */
class SimpleIntTest extends TestCase
{
    public function validValueProvider() : array
    {
        return [
            [ 0 ],
            [ 1 ],
            [ 700 ],
            [ 58760295 ],
        ];
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\SimpleIntType::fromInt
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\SimpleIntType::toInt
     */
    public function testFromIntWithValidValue(int $value) : void
    {
        $instance = SimpleIntType::fromInt($value);
        $this->assertSame($value, $instance->toInt());
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\SimpleIntType::fromIntOrNull
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\SimpleIntType::toInt
     */
    public function testFromIntOrNullWithValidValue(int $value) : void
    {
        $instance = SimpleIntType::fromIntOrNull($value);
        $this->assertSame($value, $instance->toInt());
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\SimpleIntType::fromIntOrNull
     */
    public function testFromIntOrNullWithNull() : void
    {
        $instance = SimpleIntType::fromIntOrNull(null);
        $this->assertNull($instance);
    }

    public function invalidValueProvider() : array
    {
        return [
            [ -1 ],
            [ -20 ],
        ];
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\SimpleIntType::fromInt
     */
    public function testFromIntWithInvalidValue(int $value) : void
    {
        $this->expectException(InvalidValue::class);
        SimpleIntType::fromInt($value);
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\SimpleIntType::fromInt
     */
    public function testFromIntWithInvalidValueErrorMessage(int $value) : void
    {
        $this->expectExceptionMessage(sprintf(
            'Value must be a positive number, value provided is %s.',
            (string) $value
        ));
        SimpleIntType::fromInt($value);
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\SimpleIntType::fromIntOrNull
     */
    public function testFromIntOrNullWithInvalidValue(int $value) : void
    {
        $this->expectException(InvalidValue::class);
        SimpleIntType::fromIntOrNull($value);
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\SimpleIntType::fromIntOrNull
     */
    public function testFromIntOrNullWithInvalidValueErrorMessage(int $value) : void
    {
        $this->expectExceptionMessage(sprintf(
            'Value must be a positive number, value provided is %s.',
            (string) $value
        ));
        SimpleIntType::fromIntOrNull($value);
    }
}