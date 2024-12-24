<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\IntType;

use FireMidge\Tests\ValueObject\Unit\Classes\OddIntType;
use FireMidge\ValueObject\Exception\InvalidValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(OddIntType::class)]
class OddIntTest extends TestCase
{
    public static function validValueProvider() : array
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

    #[DataProvider('validValueProvider')]
    public function testFromIntWithValidValue(int $value) : void
    {
        $instance = OddIntType::fromInt($value);
        $this->assertSame($value, $instance->toInt());
    }

    #[DataProvider('validValueProvider')]
    public function testFromIntOrNullWithValidValue(int $value) : void
    {
        $instance = OddIntType::fromIntOrNull($value);
        $this->assertSame($value, $instance->toInt());
    }

    public static function invalidValueProvider() : array
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

    #[DataProvider('invalidValueProvider')]
    public function testFromIntWithInvalidValue(int $value) : void
    {
        $this->expectException(InvalidValue::class);
        OddIntType::fromInt($value);
    }

    #[DataProvider('invalidValueProvider')]
    public function testFromIntWithInvalidValueErrorMessage(int $value, string $expectedException) : void
    {
        $this->expectExceptionMessage($expectedException);
        OddIntType::fromInt($value);
    }

    #[DataProvider('invalidValueProvider')]
    public function testFromIntOrNullWithInvalidValue(int $value) : void
    {
        $this->expectException(InvalidValue::class);
        OddIntType::fromIntOrNull($value);
    }

    #[DataProvider('invalidValueProvider')]
    public function testFromIntOrNullWithInvalidValueErrorMessage(int $value, string $expectedException) : void
    {
        $this->expectExceptionMessage($expectedException);
        OddIntType::fromIntOrNull($value);
    }
}