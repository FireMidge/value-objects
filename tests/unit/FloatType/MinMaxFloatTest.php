<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\FloatType;

use FireMidge\Tests\ValueObject\Unit\Classes\MinMaxFloatType;
use FireMidge\ValueObject\Exception\InvalidValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\MinMaxFloatType
 */
#[CoversClass(MinMaxFloatType::class)]
class MinMaxFloatTest extends TestCase
{
    public static function validValueProvider() : array
    {
        return [
            [ 11.111 ],
            [ 55.555 ],
            [ 11.112 ],
            [ 55.554 ],
            [ 12 ],
            [ 55 ],
            [ 54.5 ],
            [ 46.10 ],
        ];
    }

    #[DataProvider('validValueProvider')]
    public function testFromFloatWithValidValue(float $value) : void
    {
        $instance = MinMaxFloatType::fromFloat($value);
        $this->assertSame($value, $instance->toFloat());
    }

    #[DataProvider('validValueProvider')]
    public function testFromFloatOrNullWithValidValue(float $value) : void
    {
        $instance = MinMaxFloatType::fromFloatOrNull($value);
        $this->assertSame($value, $instance->toFloat());
    }

    public static function invalidValueProvider() : array
    {
        return [
            [ -1, 'Value must be higher than or equal to 11.111, value provided is -1' ],
            [ -20, 'Value must be higher than or equal to 11.111, value provided is -20' ],
            [ 11.110, 'Value must be higher than or equal to 11.111, value provided is 11.11' ],
            [ 11, 'Value must be higher than or equal to 11.111, value provided is 11' ],
            [ 0, 'Value must be higher than or equal to 11.111, value provided is 0' ],
            [ 1, 'Value must be higher than or equal to 11.111, value provided is 1' ],
            [ 568, 'Value must be lower than or equal to 55.555, value provided is 568' ],
            [ 55.556, 'Value must be lower than or equal to 55.555, value provided is 55.556' ],
            [ 55.6, 'Value must be lower than or equal to 55.555, value provided is 55.6' ],
            [ 569, 'Value must be lower than or equal to 55.555, value provided is 569' ],
            [ 1000, 'Value must be lower than or equal to 55.555, value provided is 1000' ],
        ];
    }

    #[DataProvider('invalidValueProvider')]
    public function testFromFloatWithInvalidValue(float $value) : void
    {
        $this->expectException(InvalidValue::class);
        MinMaxFloatType::fromFloat($value);
    }

    #[DataProvider('invalidValueProvider')]
    public function testFromFloatWithInvalidValueErrorMessage(float $value, string $expectedException) : void
    {
        $this->expectExceptionMessage($expectedException);
        MinMaxFloatType::fromFloat($value);
    }

    #[DataProvider('invalidValueProvider')]
    public function testFromFloatOrNullWithInvalidValue(float $value) : void
    {
        $this->expectException(InvalidValue::class);
        MinMaxFloatType::fromFloatOrNull($value);
    }

    #[DataProvider('invalidValueProvider')]
    public function testFromFloatOrNullWithInvalidValueErrorMessage(float $value, string $expectedException) : void
    {
        $this->expectExceptionMessage($expectedException);
        MinMaxFloatType::fromFloatOrNull($value);
    }
}