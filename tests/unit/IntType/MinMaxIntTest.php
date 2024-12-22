<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\IntType;

use FireMidge\Tests\ValueObject\Unit\Classes\MinMaxIntType;
use FireMidge\ValueObject\Exception\InvalidValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(MinMaxIntType::class)]
class MinMaxIntTest extends TestCase
{
    public static function validValueProvider() : array
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

    #[dataProvider('validValueProvider')]
    public function testFromIntWithValidValue(int $value) : void
    {
        $instance = MinMaxIntType::fromInt($value);
        $this->assertSame($value, $instance->toInt());
    }

    #[DataProvider('validValueProvider')]
    public function testFromIntOrNullWithValidValue(int $value) : void
    {
        $instance = MinMaxIntType::fromIntOrNull($value);
        $this->assertSame($value, $instance->toInt());
    }

    public static function invalidValueProvider() : array
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

    #[DataProvider('invalidValueProvider')]
    public function testFromIntWithInvalidValue(int $value) : void
    {
        $this->expectException(InvalidValue::class);
        MinMaxIntType::fromInt($value);
    }

    #[DataProvider('invalidValueProvider')]
    public function testFromIntWithInvalidValueErrorMessage(int $value, string $expectedException) : void
    {
        $this->expectExceptionMessage($expectedException);
        MinMaxIntType::fromInt($value);
    }

    #[DataProvider('invalidValueProvider')]
    public function testFromIntOrNullWithInvalidValue(int $value) : void
    {
        $this->expectException(InvalidValue::class);
        MinMaxIntType::fromIntOrNull($value);
    }

    #[DataProvider('invalidValueProvider')]
    public function testFromIntOrNullWithInvalidValueErrorMessage(int $value, string $expectedException) : void
    {
        $this->expectExceptionMessage($expectedException);
        MinMaxIntType::fromIntOrNull($value);
    }

    public function testMaximumErrorMessage() : void
    {
        $per = MinMaxIntType::fromInt(566);

        $this->expectExceptionMessage(
            'Cannot add value 2 to 566 as it brings the total (568) above the maximum value of 567'
        );
        $per->add(2);
    }

    public function testMinimumErrorMessage() : void
    {
        $per = MinMaxIntType::fromInt(200);

        $this->expectExceptionMessage(
            'Cannot subtract value 150 from 200 as it brings the total (50) below the minimum value of 123'
        );
        $per->subtract(150);
    }

    public function testMinimumErrorMessageWithAdd() : void
    {
        $per = MinMaxIntType::fromInt(250);

        $this->expectExceptionMessage(
            'Cannot add value -200 to 250 as it brings the total (50) below the minimum value of 123'
        );
        $per->add(-200);
    }

    public function testMaximumErrorMessageWithSubtract() : void
    {
        $per = MinMaxIntType::fromInt(560);

        $this->expectExceptionMessage(
            'Cannot subtract value -20 from 560 as it brings the total (580) above the maximum value of 567'
        );
        $per->subtract(-20);
    }
}