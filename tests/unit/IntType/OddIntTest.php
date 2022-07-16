<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\IntType;

use FireMidge\Tests\ValueObject\Unit\Classes\OddIntType;
use FireMidge\ValueObject\Exception\InvalidValue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\OddIntType
 */
class OddIntTest extends TestCase
{
    public function validValueProvider() : array
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
     * @covers       \FireMidge\Tests\ValueObject\Unit\Classes\OddIntType::fromInt
     * @covers       \FireMidge\Tests\ValueObject\Unit\Classes\OddIntType::toInt
     */
    public function testFromIntWithValidValue(int $value) : void
    {
        $instance = OddIntType::fromInt($value);
        $this->assertSame($value, $instance->toInt());
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers       \FireMidge\Tests\ValueObject\Unit\Classes\OddIntType::fromIntOrNull
     * @covers       \FireMidge\Tests\ValueObject\Unit\Classes\OddIntType::toInt
     */
    public function testFromIntOrNullWithValidValue(int $value) : void
    {
        $instance = OddIntType::fromIntOrNull($value);
        $this->assertSame($value, $instance->toInt());
    }

    public function invalidValueProvider() : array
    {
        return [
            [ -2, 'Only odd values allowed. Value provided: -2' ],
            [ -20, 'Only odd values allowed. Value provided: -20' ],
            [ 122, 'Only odd values allowed. Value provided: 122' ],
            [ 4, 'Only odd values allowed. Value provided: 4' ],
            [ 568, 'Only odd values allowed. Value provided: 568' ],
            [ 1000, 'Only odd values allowed. Value provided: 1000' ],
        ];
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\OddIntType::fromInt
     */
    public function testFromIntWithInvalidValue(int $value) : void
    {
        $this->expectException(InvalidValue::class);
        OddIntType::fromInt($value);
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\OddIntType::fromInt
     */
    public function testFromIntWithInvalidValueErrorMessage(int $value, string $expectedException) : void
    {
        $this->expectExceptionMessage($expectedException);
        OddIntType::fromInt($value);
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\OddIntType::fromIntOrNull
     */
    public function testFromIntOrNullWithInvalidValue(int $value) : void
    {
        $this->expectException(InvalidValue::class);
        OddIntType::fromIntOrNull($value);
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\OddIntType::fromIntOrNull
     */
    public function testFromIntOrNullWithInvalidValueErrorMessage(int $value, string $expectedException) : void
    {
        $this->expectExceptionMessage($expectedException);
        OddIntType::fromIntOrNull($value);
    }
}