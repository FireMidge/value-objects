<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\CollectionType;

use FireMidge\Tests\ValueObject\Unit\Classes\BoolType;
use FireMidge\Tests\ValueObject\Unit\Classes\DoubleObject;
use FireMidge\Tests\ValueObject\Unit\Classes\DynamicClassCollectionType;
use FireMidge\Tests\ValueObject\Unit\Classes\IntArrayEnumType;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleFloatType;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleIntType;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleStringType;
use FireMidge\ValueObject\Exception\ConversionError;
use FireMidge\ValueObject\Exception\InvalidValue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\DynamicClassCollectionType
 * @covers \FireMidge\ValueObject\Exception\InvalidValue
 * @uses \FireMidge\Tests\ValueObject\Unit\Classes\SimpleIntType
 * @uses \FireMidge\Tests\ValueObject\Unit\Classes\SimpleStringType
 * @uses \FireMidge\Tests\ValueObject\Unit\Classes\SimpleFloatType
 * @uses \FireMidge\Tests\ValueObject\Unit\Classes\IntArrayEnumType
 */
class DynamicClassCollectionTest extends TestCase
{
    public function testFromRawValuesArrayWithBoolType() : void
    {
        DynamicClassCollectionType::useClass(BoolType::class);
        $sut = DynamicClassCollectionType::fromRawArray([true, false, false, true]);

        $this->assertEquals([
            new BoolType(true),
            new BoolType(false),
            new BoolType(false),
            new BoolType(true),
        ], $sut->toArray());
    }

    public function testFromRawValuesArrayWithFloatType() : void
    {
        DynamicClassCollectionType::useClass(SimpleFloatType::class);
        $sut = DynamicClassCollectionType::fromRawArray([
            3.5,
            5.78,
            6.9999999,
            567765804.00000001,
            80, // integer
            '70.58', // string
        ]);

        $this->assertEquals([
            SimpleFloatType::fromFloat(3.5),
            SimpleFloatType::fromFloat(5.78),
            SimpleFloatType::fromFloat(6.9999999),
            SimpleFloatType::fromFloat(567765804.00000001),
            SimpleFloatType::fromFloat(80.0),
            SimpleFloatType::fromFloat(70.58),
        ], $sut->toArray());
    }

    public function testFromRawValuesArrayWithDoubleType() : void
    {
        DynamicClassCollectionType::useClass(DoubleObject::class);
        $sut = DynamicClassCollectionType::fromRawArray([
            3.5,
            5.78,
            6.9999999,
            567765804.00000001,
            80, // integer, which still works because PHP.
        ]);

        $this->assertEquals([
            DoubleObject::fromDouble(3.5),
            DoubleObject::fromDouble(5.78),
            DoubleObject::fromDouble(6.9999999),
            DoubleObject::fromDouble(567765804.00000001),
            DoubleObject::fromDouble(80.0),
        ], $sut->toArray());
    }

    public function testFromRawValuesArrayWithArrayType() : void
    {
        DynamicClassCollectionType::useClass(IntArrayEnumType::class);
        $sut = DynamicClassCollectionType::fromRawArray([
            [11, 22],
            [22],
            [33, 11],
        ]);

        $this->assertEquals([
            IntArrayEnumType::fromArray([11, 22]),
            IntArrayEnumType::fromArray([22]),
            IntArrayEnumType::fromArray([33, 11]),
        ], $sut->toArray());
    }

    /**
     * Checking that the error message we get from passing invalid values is still useful.
     */
    public function testFromRawValuesArrayWithArrayTypeWithInvalidValues() : void
    {
        DynamicClassCollectionType::useClass(IntArrayEnumType::class);

        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('The following values are not valid: "44".');

        DynamicClassCollectionType::fromRawArray([
            [11, 44],
        ]);
    }

    public function nonConvertableValueProvider() : array
    {
        return [
            [ SimpleIntType::class, 3.1, '3.1' ],
            [ SimpleIntType::class, true, 'true' ],
            [ SimpleIntType::class, [3], 'Array(3)' ],
            [ SimpleIntType::class, [3, 5], 'Array(3, 5)' ],
            [ SimpleStringType::class, ['HelloWorld'], 'Array("HelloWorld")' ],
            [ SimpleStringType::class, 385, '385' ],
            [ SimpleStringType::class, false, 'false' ],
            [ SimpleStringType::class, 1.85, '1.85' ],
        ];
    }

    /**
     * @dataProvider nonConvertableValueProvider
     */
    public function testFromRawValuesArrayWithNonConvertableValue(
        string $classFqn,
        mixed $value,
        string $expectedRenderedValue
    ) : void
    {
        DynamicClassCollectionType::useClass($classFqn);

        $this->expectException(ConversionError::class);
        $this->expectExceptionMessage(sprintf(
            'Could not convert value %s to %s',
            $expectedRenderedValue,
            $classFqn
        ));

        DynamicClassCollectionType::fromRawArray([$value]);
    }

    public function testFromRawValuesWithCustomCallbackReturningInvalidType() : void
    {
        DynamicClassCollectionType::useClass(BoolType::class);

        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('Callback is returning the wrong type: Invalid value. Must be of type "object" but got "boolean"');

        DynamicClassCollectionType::fromRawArray([7, -5, 0, 1, -11], fn($v) => $v > 0);
    }

    public function testFromRawValuesWithCustomCallback() : void
    {
        DynamicClassCollectionType::useClass(BoolType::class);
        $sut = DynamicClassCollectionType::fromRawArray([7, -5, 0, 1, -11], fn($v) => new BoolType($v > 0));

        $this->assertEquals([
            new BoolType(true),
            new BoolType(false),
            new BoolType(false),
            new BoolType(true),
            new BoolType(false),
        ], $sut->toArray());
    }
}