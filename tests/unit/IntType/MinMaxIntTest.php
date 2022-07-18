<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\IntType;

use FireMidge\Tests\ValueObject\Unit\Classes\MinMaxIntType;
use FireMidge\ValueObject\Exception\InvalidValue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\MinMaxIntType
 */
class MinMaxIntTest extends TestCase
{
    public function validValueProvider() : array
    {
        return [
            [ 123 ],
            [ 567 ],
            [ 124 ],
            [ 566 ],
            [ 500 ],
            [ 259 ],
        ];
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers       \FireMidge\Tests\ValueObject\Unit\Classes\MinMaxIntType::fromInt
     * @covers       \FireMidge\Tests\ValueObject\Unit\Classes\MinMaxIntType::toInt
     */
    public function testFromIntWithValidValue(int $value) : void
    {
        $instance = MinMaxIntType::fromInt($value);
        $this->assertSame($value, $instance->toInt());
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers       \FireMidge\Tests\ValueObject\Unit\Classes\MinMaxIntType::fromIntOrNull
     * @covers       \FireMidge\Tests\ValueObject\Unit\Classes\MinMaxIntType::toInt
     */
    public function testFromIntOrNullWithValidValue(int $value) : void
    {
        $instance = MinMaxIntType::fromIntOrNull($value);
        $this->assertSame($value, $instance->toInt());
    }

    public function invalidValueProvider() : array
    {
        return [
            [ -1, 'Value must be higher than or equal to 123, value provided is -1' ],
            [ -20, 'Value must be higher than or equal to 123, value provided is -20' ],
            [ 122, 'Value must be higher than or equal to 123, value provided is 122' ],
            [ 0, 'Value must be higher than or equal to 123, value provided is 0' ],
            [ 1, 'Value must be higher than or equal to 123, value provided is 1' ],
            [ 568, 'Value must be lower than or equal to 567, value provided is 568' ],
            [ 569, 'Value must be lower than or equal to 567, value provided is 569' ],
            [ 1000, 'Value must be lower than or equal to 567, value provided is 1000' ],
        ];
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\MinMaxIntType::fromInt
     */
    public function testFromIntWithInvalidValue(int $value) : void
    {
        $this->expectException(InvalidValue::class);
        MinMaxIntType::fromInt($value);
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\MinMaxIntType::fromInt
     */
    public function testFromIntWithInvalidValueErrorMessage(int $value, string $expectedException) : void
    {
        $this->expectExceptionMessage($expectedException);
        MinMaxIntType::fromInt($value);
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\MinMaxIntType::fromIntOrNull
     */
    public function testFromIntOrNullWithInvalidValue(int $value) : void
    {
        $this->expectException(InvalidValue::class);
        MinMaxIntType::fromIntOrNull($value);
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\MinMaxIntType::fromIntOrNull
     */
    public function testFromIntOrNullWithInvalidValueErrorMessage(int $value, string $expectedException) : void
    {
        $this->expectExceptionMessage($expectedException);
        MinMaxIntType::fromIntOrNull($value);
    }
}