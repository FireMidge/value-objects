<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\CollectionType;

use FireMidge\Tests\ValueObject\Unit\Classes\MinMaxIntType;
use FireMidge\Tests\ValueObject\Unit\Classes\OddIntType;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleIntType;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleObject;
use FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType;
use FireMidge\ValueObject\Exception\InvalidValue;
use FireMidge\ValueObject\Exception\ValueNotFound;
use PHPUnit\Framework\TestCase;
use stdClass;

class IntVOCollectionTest extends TestCase
{
    public function validValueProvider() : array
    {
        return [
            [ [], [] ],
            [ [ MinMaxIntType::fromInt(400) ] ],
            [ [ MinMaxIntType::fromInt(126), MinMaxIntType::fromInt(350) ] ],
            [ [ MinMaxIntType::fromInt(350), MinMaxIntType::fromInt(350) ] ],
        ];
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::fromArray
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::toArray
     */
    public function testFromArrayWithValidValue(array $values) : void
    {
        $instance = IntVOCollectionType::fromArray($values);
        $this->assertSame($values, $instance->toArray());
    }

    public function invalidValueProvider() : array
    {
        return [
            [ [ 1 ], 'Invalid value. Must be an object and an instance of "FireMidge\Tests\ValueObject\Unit\Classes\MinMaxIntType"' ],
            [ [ 1, 12.5 ], 'Invalid value. Must be an object and an instance of "FireMidge\Tests\ValueObject\Unit\Classes\MinMaxIntType"' ],
            [
                [ new SimpleObject('Lorem') ],
                'Invalid value. Must be an instance of "FireMidge\Tests\ValueObject\Unit\Classes\MinMaxIntType", '
                . 'but is "FireMidge\Tests\ValueObject\Unit\Classes\SimpleObject"'
            ],
        ];
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::fromArray
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::toArray
     */
    public function testFromArrayWithInvalidValue(array $input, string $errorMessage) : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($errorMessage);

        IntVOCollectionType::fromArray($input);
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::contains
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::fromArray
     */
    public function testContainsWithEmptyArray() : void
    {
        $instance = IntVOCollectionType::fromArray([]);
        $this->assertFalse($instance->contains(MinMaxIntType::fromInt(400)));
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::contains
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::fromArray
     */
    public function testContainsWithDifferentValue() : void
    {
        $instance = IntVOCollectionType::fromArray([
            MinMaxIntType::fromInt(400)
        ]);
        $this->assertFalse($instance->contains(MinMaxIntType::fromInt(401)));
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::contains
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::fromArray
     */
    public function testContainsWithMultipleValues() : void
    {
        $instance = IntVOCollectionType::fromArray([
            MinMaxIntType::fromInt(400),
            MinMaxIntType::fromInt(500),
            MinMaxIntType::fromInt(450),
            MinMaxIntType::fromInt(450),
        ]);

        $this->assertTrue($instance->contains(MinMaxIntType::fromInt(400)), 'Expected to contain "400"');
        $this->assertTrue($instance->contains(MinMaxIntType::fromInt(500)), 'Expected to contain "500"');
        $this->assertTrue($instance->contains(MinMaxIntType::fromInt(450)), 'Expected to contain "450"');
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::contains
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::fromArray
     */
    public function testContainsThrowsExceptionWithInvalidType() : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage(
            'Invalid value. Must be an object and an instance of "FireMidge\Tests\ValueObject\Unit\Classes\MinMaxIntType"'
        );

        $instance = IntVOCollectionType::fromArray([
            MinMaxIntType::fromInt(400)
        ]);
        $this->assertFalse($instance->contains(400));
    }

    public function singleValidValueProvider() : array
    {
        return [
            '400' => [ MinMaxIntType::fromInt(400) ],
            '450' => [ MinMaxIntType::fromInt(450) ],
            '135' => [ MinMaxIntType::fromInt(135) ],
        ];
    }

    /**
     * @dataProvider singleValidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::withValue
     */
    public function testWithValueWithValidValue(MinMaxIntType $value) : void
    {
        $instance    = IntVOCollectionType::fromArray([
            MinMaxIntType::fromInt(137),
            MinMaxIntType::fromInt(128),
            MinMaxIntType::fromInt(135),
        ]);
        $newInstance = $instance->withValue($value);

        $this->assertEquals([
            MinMaxIntType::fromInt(137),
            MinMaxIntType::fromInt(128),
            MinMaxIntType::fromInt(135),
            $value
        ], $newInstance->toArray(), 'Expected new instance to match');
        $this->assertEquals([
            MinMaxIntType::fromInt(137),
            MinMaxIntType::fromInt(128),
            MinMaxIntType::fromInt(135),
        ], $instance->toArray(), 'Expected old instance to have remained unchanged');
    }

    public function singleInvalidValueProvider() : array
    {
        return [
            'string' => [
                '400',
                'Invalid value. Must be an object and an instance of "FireMidge\Tests\ValueObject\Unit\Classes\MinMaxIntType"',
            ],
            'int'    => [
                1,
                'Invalid value. Must be an object and an instance of "FireMidge\Tests\ValueObject\Unit\Classes\MinMaxIntType"',
            ],
            'bool'   => [
                false,
                'Invalid value. Must be an object and an instance of "FireMidge\Tests\ValueObject\Unit\Classes\MinMaxIntType"',
            ],
            'object' => [
                new SimpleObject('name'),
                'Invalid value. Must be an instance of "FireMidge\Tests\ValueObject\Unit\Classes\MinMaxIntType", '
                . 'but is "FireMidge\Tests\ValueObject\Unit\Classes\SimpleObject"'
            ],
        ];
    }

    /**
     * @dataProvider singleInvalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::withValue
     */
    public function testWithValueWithInvalidValue($invalidValue, string $expectedExceptionMessage) : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $instance = IntVOCollectionType::fromArray([
            MinMaxIntType::fromInt(488),
        ]);
        $instance->withValue($invalidValue);
    }

    public function invalidWithoutValueProvider() : array
    {
        return [
            'invalidOne' => [
                [ MinMaxIntType::fromInt(400) ],
                '400',
                InvalidValue::class,
                'Invalid value. Must be an object and an instance of "FireMidge\Tests\ValueObject\Unit\Classes\MinMaxIntType"',
            ],
            'invalidTwo' => [
                [ MinMaxIntType::fromInt(400) ],
                400,
                InvalidValue::class,
                'Invalid value. Must be an object and an instance of "FireMidge\Tests\ValueObject\Unit\Classes\MinMaxIntType"',
            ],
            'invalidThree' => [
                [ MinMaxIntType::fromInt(400) ],
                MinMaxIntType::fromInt(401),
                ValueNotFound::class,
                'Value "401" was not found. Available values: "400"'
            ],
            'invalidFour' => [
                [ MinMaxIntType::fromInt(400), MinMaxIntType::fromInt(401), MinMaxIntType::fromInt(402) ],
                MinMaxIntType::fromInt(405),
                ValueNotFound::class,
                'Value "405" was not found. Available values: "400", "401", "402"'
            ],
            'invalidFive' => [
                [ MinMaxIntType::fromInt(400), MinMaxIntType::fromInt(401), MinMaxIntType::fromInt(400) ],
                MinMaxIntType::fromInt(402),
                ValueNotFound::class,
                'Value "402" was not found. Available values: "400", "401", "400"'
            ],
            'invalidSix' => [
                [ MinMaxIntType::fromInt(400) ],
                SimpleIntType::fromInt(400),
                InvalidValue::class,
                'Invalid value. Must be an instance of "FireMidge\Tests\ValueObject\Unit\Classes\MinMaxIntType", '
                . 'but is "FireMidge\Tests\ValueObject\Unit\Classes\SimpleIntType"',
            ],
        ];
    }

    /**
     * @dataProvider invalidWithoutValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::tryWithoutValue
     */
    public function testTryWithoutValueWithInvalidValue(
        array $stateBefore,
        $valueToBeRemoved,
        string $expectedExceptionClass,
        string $expectedErrorMessagePart
    ) : void
    {
        $this->expectException($expectedExceptionClass);
        $this->expectExceptionMessage($expectedErrorMessagePart);

        $instance = IntVOCollectionType::fromArray($stateBefore);
        $instance->tryWithoutValue($valueToBeRemoved);
    }

    public function withoutValidValueProvider() : array
    {
        return [
            'one' => [
                [ MinMaxIntType::fromInt(400), MinMaxIntType::fromInt(401), MinMaxIntType::fromInt(402) ],
                MinMaxIntType::fromInt(401),
                [ MinMaxIntType::fromInt(400), MinMaxIntType::fromInt(402) ],
            ],
            'two' => [
                [ MinMaxIntType::fromInt(400), MinMaxIntType::fromInt(401), MinMaxIntType::fromInt(402) ],
                MinMaxIntType::fromInt(400),
                [ MinMaxIntType::fromInt(401), MinMaxIntType::fromInt(402) ],
            ],
            'three' => [
                [ MinMaxIntType::fromInt(400), MinMaxIntType::fromInt(401), MinMaxIntType::fromInt(402) ],
                MinMaxIntType::fromInt(402),
                [ MinMaxIntType::fromInt(400), MinMaxIntType::fromInt(401) ],
            ],
            'four' => [
                [ MinMaxIntType::fromInt(400) ],
                MinMaxIntType::fromInt(400),
                [ ],
            ],
            'five' => [
                [ MinMaxIntType::fromInt(401), MinMaxIntType::fromInt(402) ],
                MinMaxIntType::fromInt(401),
                [ MinMaxIntType::fromInt(402) ],
            ],
        ];
    }

    /**
     * @dataProvider withoutValidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::tryWithoutValue
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::toArray
     */
    public function testTryWithoutValueDoesNotChangePreExisting(
        array $stateBefore,
        MinMaxIntType $valueToBeRemoved,
        array $stateAfter
    ) : void
    {
        $instance = IntVOCollectionType::fromArray($stateBefore);
        $newInstance = $instance->tryWithoutValue($valueToBeRemoved);

        $this->assertEquals($stateAfter, $newInstance->toArray(), 'Expected new instance to match');
        $this->assertEquals($stateBefore, $instance->toArray(), 'Expected old instance to have remained unchanged');
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::withoutValue
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::toArray
     */
    public function testWithoutValueNotThrowingExceptionIfValueDidNotExist() : void
    {
        $instance = IntVOCollectionType::fromArray([
            MinMaxIntType::fromInt(401),
            MinMaxIntType::fromInt(402),
        ]);
        $newInstance = $instance->withoutValue(MinMaxIntType::fromInt(403));

        $this->assertEquals([
            MinMaxIntType::fromInt(401),
            MinMaxIntType::fromInt(402),
        ], $newInstance->toArray());
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::withoutValue
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::toArray
     */
    public function testWithoutValueThrowingExceptionIfValueInvalid() : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage(
            'Invalid value. Must be an instance of "FireMidge\Tests\ValueObject\Unit\Classes\MinMaxIntType", '
            . 'but is "FireMidge\Tests\ValueObject\Unit\Classes\OddIntType"',
        );

        $instance = IntVOCollectionType::fromArray([
            MinMaxIntType::fromInt(401),
            MinMaxIntType::fromInt(402),
        ]);
        $instance->withoutValue(OddIntType::fromInt(401));
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::isEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::isNotEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::fromArray
     */
    public function testIsEqualToInstanceOfSameClassSuccessful() : void
    {
        $instance1 = IntVOCollectionType::fromArray([
            MinMaxIntType::fromInt(401),
            MinMaxIntType::fromInt(402),
        ]);
        $instance2 = IntVOCollectionType::fromArray([
            MinMaxIntType::fromInt(402),
            MinMaxIntType::fromInt(401),
        ]);

        $this->assertTrue($instance1->isEqualTo($instance2), 'Expected instance1 to be equal to instance2');
        $this->assertTrue($instance2->isEqualTo($instance1), 'Expected instance2 to be equal to instance1');
        $this->assertFalse($instance1->isNotEqualTo($instance2), 'Expected isNotEqualTo to return false for instance1');
        $this->assertFalse($instance2->isNotEqualTo($instance1), 'Expected isNotEqualTo to return false for instance2');
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::isEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::isNotEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::fromArray
     */
    public function testIsEqualToInstanceOfSameClassNotEqual() : void
    {
        $instance1 = IntVOCollectionType::fromArray([
            MinMaxIntType::fromInt(401),
            MinMaxIntType::fromInt(402),
        ]);
        $instance2 = IntVOCollectionType::fromArray([
            MinMaxIntType::fromInt(402),
        ]);

        $this->assertFalse($instance1->isEqualTo($instance2), 'Expected instance1 not to be equal to instance2');
        $this->assertFalse($instance2->isEqualTo($instance1), 'Expected instance2 not to be equal to instance1');
        $this->assertTrue($instance1->isNotEqualTo($instance2), 'Expected isNotEqualTo to return true for instance1');
        $this->assertTrue($instance2->isNotEqualTo($instance1), 'Expected isNotEqualTo to return true for instance2');
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::isEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::isNotEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::fromArray
     */
    public function testIsEqualToArraySuccessful() : void
    {
        $instance1 = IntVOCollectionType::fromArray([
            MinMaxIntType::fromInt(401),
            MinMaxIntType::fromInt(402),
        ]);
        $array = [
            MinMaxIntType::fromInt(402),
            MinMaxIntType::fromInt(401),
        ];

        $this->assertTrue($instance1->isEqualTo($array));
        $this->assertFalse($instance1->isNotEqualTo($array));
    }

    public function notEqualProvider() : array
    {
        return [
            'differentCount'  => [[MinMaxIntType::fromInt(402)]],
            'differentValues' => [[MinMaxIntType::fromInt(402), MinMaxIntType::fromInt(403)]],
        ];
    }

    /**
     * @dataProvider notEqualProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::isEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::isNotEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::fromArray
     */
    public function testIsEqualToArrayNotEqual(array $valuesToCompareTo) : void
    {
        $instance1 = IntVOCollectionType::fromArray([
            MinMaxIntType::fromInt(401),
            MinMaxIntType::fromInt(402),
        ]);

        $this->assertFalse($instance1->isEqualTo($valuesToCompareTo));
        $this->assertTrue($instance1->isNotEqualTo($valuesToCompareTo));
    }

    /**
     * @dataProvider notEqualProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::isEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::isNotEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::fromArray
     */
    public function testIsEqualToStandardObjectNotEqual(array $valuesToCompareTo) : void
    {
        $instance1 = IntVOCollectionType::fromArray([
            MinMaxIntType::fromInt(401),
            MinMaxIntType::fromInt(402),
        ]);

        $object = new stdClass();
        foreach ($valuesToCompareTo as $k => $v) {
            $propertyName          = 'property' . $k;
            $object->$propertyName = $v;
        }

        $this->assertFalse($instance1->isEqualTo($object));
        $this->assertTrue($instance1->isNotEqualTo($object));
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::isEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::isNotEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::fromArray
     */
    public function testIsEqualToStandardObjectSuccessful() : void
    {
        $instance1 = IntVOCollectionType::fromArray([
            MinMaxIntType::fromInt(401),
            MinMaxIntType::fromInt(402),
        ]);

        $object         = new stdClass();
        $object->first  = MinMaxIntType::fromInt(402);
        $object->second = MinMaxIntType::fromInt(401);

        $this->assertTrue($instance1->isEqualTo($object));
        $this->assertFalse($instance1->isNotEqualTo($object));
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::isEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::isNotEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::fromArray
     */
    public function testIsEqualToStandardObjectEqualWithDifferentTypes() : void
    {
        $instance1 = IntVOCollectionType::fromArray([
            MinMaxIntType::fromInt(401),
            MinMaxIntType::fromInt(402),
        ]);

        $object         = new stdClass();
        $object->first  = 402;
        $object->second = 401;

        $this->assertTrue($instance1->isEqualTo($object));
        $this->assertFalse($instance1->isNotEqualTo($object));
    }


    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::isEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::isNotEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::fromArray
     */
    public function testIsEqualToArrayEqualWithDifferentTypes() : void
    {
        $instance1 = IntVOCollectionType::fromArray([
            MinMaxIntType::fromInt(401),
            MinMaxIntType::fromInt(402),
        ]);
        $array = [402, 401];

        $this->assertTrue($instance1->isEqualTo($array));
        $this->assertFalse($instance1->isNotEqualTo($array));
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::count
     */
    public function testCount() : void
    {
        $instance = IntVOCollectionType::fromArray([
            MinMaxIntType::fromInt(401),
            MinMaxIntType::fromInt(402),
            MinMaxIntType::fromInt(403),
        ]);

        $this->assertSame(3, $instance->count());
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::empty
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntVOCollectionType::count
     */
    public function testEmptyFactoryMethod() : void
    {
        $instance = IntVOCollectionType::empty();

        $this->assertCount(0, $instance->toArray());
        $this->assertSame(0, $instance->count());
    }
}