<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\ArrayEnumType;

use FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleObject;
use FireMidge\ValueObject\Exception\InvalidValue;
use FireMidge\ValueObject\Exception\ValueNotFound;
use PHPUnit\Framework\TestCase;

/**
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType
 */
class ObjectArrayEnumTest extends TestCase
{
    public function validValueProvider() : array
    {
        return [
            [ [] ],
            [ [new SimpleObject('A')] ],
            [ [new SimpleObject('B')] ],
            [ [new SimpleObject('C')] ],
            [ [new SimpleObject('C'), new SimpleObject('B')] ],
            [ [new SimpleObject('B'), new SimpleObject('B')] ],
            [ [new SimpleObject('A'), new SimpleObject('B'), new SimpleObject('C')] ],
        ];
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType::fromArray
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType::toArray
     */
    public function testFromArrayWithValidValue(array $values) : void
    {
        $instance = ObjectArrayEnumType::fromArray($values);
        $this->assertSame($values, $instance->toArray());
    }

    public function invalidValueProvider() : array
    {
        return [
            'a' => [
                [ 'A' ],
                'Invalid value. Must be an object and an instance of "FireMidge\Tests\ValueObject\Unit\Classes\SimpleObject"'
            ],
            'd' => [
                [ new SimpleObject('D') ],
                'The following values are not valid: "D". Valid values are: "A", "B", "C"'
            ],
            'a2' => [
                [ 'A', new SimpleObject('C')],
                'Invalid value. Must be an object and an instance of "FireMidge\Tests\ValueObject\Unit\Classes\SimpleObject"'
            ],
            'd2' => [
                [ new SimpleObject('C'), new SimpleObject('B'), new SimpleObject('D') ],
                'The following values are not valid: "D". Valid values are: "A", "B", "C"'
            ],
            'x' => [
                [new SimpleObject('B'), new SimpleObject('X'), new SimpleObject('B')],
                'The following values are not valid: "X". Valid values are: "A", "B", "C"'
            ],
        ];
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType::fromArray
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType::toArray
     */
    public function testFromArrayWithInvalidValue(array $values, string $expectedExceptionMessage) : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        ObjectArrayEnumType::fromArray($values);
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType::withAll
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType::toArray
     */
    public function testWithAll() : void
    {
        $instance = ObjectArrayEnumType::withAll();
        $this->assertEquals([
            new SimpleObject('A'),
            new SimpleObject('B'),
            new SimpleObject('C'),
        ], $instance->toArray());
    }


    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType::fromArray
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType::toArray
     */
    public function testFromArrayWithEmptyArray() : void
    {
        $instance = ObjectArrayEnumType::fromArray([]);
        $this->assertSame([], $instance->toArray());
    }

    public function singleValidValueProvider() : array
    {
        return [
            'a' => [ new SimpleObject('A') ],
            'b' => [ new SimpleObject('B') ],
            'c' => [ new SimpleObject('C') ],
        ];
    }

    /**
     * @dataProvider singleValidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType::withValue
     *
     * @depends testFromArrayWithEmptyArray
     */
    public function testWithValueWithValidValue(SimpleObject $value) : void
    {
        $instance    = ObjectArrayEnumType::fromArray([]);
        $newInstance = $instance->withValue($value);

        $this->assertSame([$value], $newInstance->toArray(), 'Expected new instance to match');
        $this->assertSame([], $instance->toArray(), 'Expected old instance to have remained unchanged'); // Make sure the previous instance hasn't been changed
    }

    public function singleInvalidValueProvider() : array
    {
        return [
            'a' => [
                'A',
                'Invalid value. Must be an object and an instance of "FireMidge\Tests\ValueObject\Unit\Classes\SimpleObject"'
            ],
            'd' => [
                new SimpleObject('D'),
                'The following values are not valid: "D". Valid values are: "A", "B", "C"'
            ],
        ];
    }

    /**
     * @dataProvider singleInvalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType::withValue
     *
     * @depends testFromArrayWithEmptyArray
     */
    public function testWithValueWithInvalidValue($invalidValue, string $expectedExceptionMessage) : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $instance = ObjectArrayEnumType::fromArray([]);
        $instance->withValue($invalidValue);
    }

    /**
     * @dataProvider singleValidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType::withValue
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType::toArray
     */
    public function testWithValueDoesNotChangePreExisting(SimpleObject $value) : void
    {
        $instance = ObjectArrayEnumType::fromArray([
            new SimpleObject('B')
        ]);
        $newInstance = $instance->withValue($value);

        $this->assertEquals([
            new SimpleObject('B'), // expecting this as it previously existed, and we just added a value
            $value
        ], $newInstance->toArray(), 'Expected new instance to match');
        $this->assertEquals([
            new SimpleObject('B')
        ], $instance->toArray(), 'Expected old instance to have remained unchanged');
    }

    public function withoutValidValueProvider() : array
    {
        return [
            'a-a' => [
                [ new SimpleObject('A') ],
                new SimpleObject('A'),
                [ ],
            ],
            'b-b' => [
                [ new SimpleObject('B') ],
                new SimpleObject('B'),
                [ ],
            ],
            'c-c' => [
                [ new SimpleObject('C') ],
                new SimpleObject('C'),
                [ ],
            ],
            'cc-c' => [
                [ new SimpleObject('C'), new SimpleObject('C') ],
                new SimpleObject('C'),
                [ new SimpleObject('C') ],
            ],
            'ac-c' => [
                [ new SimpleObject('A'), new SimpleObject('C') ],
                new SimpleObject('C'),
                [ new SimpleObject('A') ],
            ],
            'acb-c' => [
                [ new SimpleObject('A'), new SimpleObject('C'), new SimpleObject('B') ],
                new SimpleObject('C'),
                [ new SimpleObject('A'), new SimpleObject('B') ],
            ],
        ];
    }

    /**
     * @dataProvider withoutValidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType::tryWithoutValue
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType::toArray
     */
    public function testTryWithoutValueDoesNotChangePreExisting(
        array $stateBefore,
        SimpleObject $valueToBeRemoved,
        array $stateAfter
    ) : void
    {
        $instance = ObjectArrayEnumType::fromArray($stateBefore);
        $newInstance = $instance->tryWithoutValue($valueToBeRemoved);

        $this->assertEquals($stateAfter, $newInstance->toArray(), 'Expected new instance to match');
        $this->assertEquals($stateBefore, $instance->toArray(), 'Expected old instance to have remained unchanged');
    }

    /**
     * @dataProvider withoutValidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType::withoutValue
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType::toArray
     */
    public function testWithoutValueDoesNotChangePreExisting(
        array $stateBefore,
        SimpleObject $valueToBeRemoved,
        array $stateAfter
    ) : void
    {
        $instance = ObjectArrayEnumType::fromArray($stateBefore);
        $newInstance = $instance->withoutValue($valueToBeRemoved);

        $this->assertEquals($stateAfter, $newInstance->toArray(), 'Expected new instance to match');
        $this->assertEquals($stateBefore, $instance->toArray(), 'Expected old instance to have remained unchanged');
    }

    public function withoutInvalidValueProvider() : array
    {
        return [
            'a-a' => [
                [ new SimpleObject('A') ],
                'A',
                InvalidValue::class,
                'Invalid value. Must be an object and an instance of "FireMidge\Tests\ValueObject\Unit\Classes\SimpleObject"',
            ],
            'a-d' => [
                [ new SimpleObject('A') ],
                new SimpleObject('D'),
                ValueNotFound::class,
                'Value "D" was not found. Available values: "A"'
            ],
        ];
    }

    /**
     * @dataProvider withoutInvalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType::tryWithoutValue
     *
     * @depends testFromArrayWithEmptyArray
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

        $instance = ObjectArrayEnumType::fromArray($stateBefore);
        $instance->tryWithoutValue($valueToBeRemoved);
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType::withoutValue
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType::toArray
     */
    public function testWithoutValueNotThrowingExceptionIfValueDidNotExist() : void
    {
        $instance = ObjectArrayEnumType::fromArray([new SimpleObject('B')]);
        $newInstance = $instance->withoutValue(new SimpleObject('A'));

        $this->assertEquals([new SimpleObject('B')], $newInstance->toArray());
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType::withoutValue
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType::toArray
     */
    public function testWithoutValueThrowingExceptionIfValueInvalid() : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('Invalid value. Must be an object and an instance of "FireMidge\Tests\ValueObject\Unit\Classes\SimpleObject"');

        $instance = ObjectArrayEnumType::fromArray([new SimpleObject('B')]);
        $instance->withoutValue('B');
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType::contains
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType::fromArray
     */
    public function testContains() : void
    {
        $instance = ObjectArrayEnumType::fromArray([
            new SimpleObject('A'),
            new SimpleObject('B'),
            new SimpleObject('A'),
        ]);

        $this->assertTrue($instance->contains(new SimpleObject('A')), 'Expected to contain A');
        $this->assertTrue($instance->contains(new SimpleObject('B')), 'Expected to contain B');
        $this->assertFalse($instance->contains(new SimpleObject('C')), 'Expected not to contain C');
        $this->assertFalse(
            $instance->contains(new SimpleObject('D')),
            'Expected not to contain invalid value D'
        );
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType::contains
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType::fromArray
     */
    public function testContainsThrowingError() : void
    {
        $instance = ObjectArrayEnumType::fromArray([
            new SimpleObject('A'),
        ]);

        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage(
            'Invalid value. Must be an object and an instance of "FireMidge\Tests\ValueObject\Unit\Classes\SimpleObject"'
        );

        $instance->contains('A');
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType::isEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType::isNotEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType::fromArray
     */
    public function testIsEqualWithSameTypeSuccessful() : void
    {
        $instance1 = ObjectArrayEnumType::fromArray([
            new SimpleObject('A'),
            new SimpleObject('B'),
        ]);

        $instance2 = ObjectArrayEnumType::fromArray([
            new SimpleObject('B'),
            new SimpleObject('A'),
        ]);

        $this->assertTrue($instance1->isEqualTo($instance2));
        $this->assertFalse($instance1->isNotEqualTo($instance2));
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType::isEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType::isNotEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType::fromArray
     */
    public function testIsEqualWithArraySuccessful() : void
    {
        $instance = ObjectArrayEnumType::fromArray([
            new SimpleObject('A'),
            new SimpleObject('B'),
        ]);

        $array = ['A', 'B'];

        $this->assertTrue($instance->isEqualTo($array));
        $this->assertFalse($instance->isNotEqualTo($array));
    }
}