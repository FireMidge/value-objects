<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\IntEnumType;

use FireMidge\Tests\ValueObject\Unit\Classes\IntEnumType;
use FireMidge\ValueObject\Exception\InvalidValue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntEnumType
 */
class IntEnumTest extends TestCase
{
    public function validValueProvider() : array
    {
        return [
            [IntEnumType::SPRING ],
            [IntEnumType::SUMMER ],
            [IntEnumType::AUTUMN ],
            [IntEnumType::WINTER ],
        ];
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntEnumType::fromInt
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntEnumType::toInt
     */
    public function testFromIntWithValidValue(int $value) : void
    {
        $instance = IntEnumType::fromInt($value);
        $this->assertSame($value, $instance->toInt());
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntEnumType::fromIntOrNull
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntEnumType::toInt
     */
    public function testFromIntOrNullWithValidValue(int $value) : void
    {
        $instance = IntEnumType::fromIntOrNull($value);
        $this->assertSame($value, $instance->toInt());
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntEnumType::fromIntOrNull
     */
    public function testFromIntOrNullWithNull() : void
    {
        $instance = IntEnumType::fromIntOrNull(null);
        $this->assertNull($instance);
    }

    public function invalidValueProvider() : array
    {
        return [
            [ 0 ],
            [ 5 ],
            [ -1 ],
        ];
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntEnumType::fromInt
     */
    public function testFromIntWithInvalidValue(int $value) : void
    {
        $this->expectException(InvalidValue::class);
        IntEnumType::fromInt($value);
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntEnumType::fromInt
     */
    public function testFromIntWithInvalidValueErrorMessage(int $value) : void
    {
        $this->expectExceptionMessage(sprintf(
            'Value "%s" is invalid. Must be one of: "1", "2", "3", "4"',
            $value
        ));
        IntEnumType::fromInt($value);
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntEnumType::fromIntOrNull
     */
    public function testFromIntOrNullWithInvalidValue(int $value) : void
    {
        $this->expectException(InvalidValue::class);
        IntEnumType::fromIntOrNull($value);
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntEnumType::fromIntOrNull
     */
    public function testFromIntOrNullWithInvalidValueErrorMessage(int $value) : void
    {
        $this->expectExceptionMessage(sprintf(
            'Value "%s" is invalid. Must be one of: "1", "2", "3", "4"',
            $value
        ));
        IntEnumType::fromIntOrNull($value);
    }
}