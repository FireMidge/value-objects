<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\ArrayEnumType;

use FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleObject;
use FireMidge\ValueObject\Exception\InvalidValue;
use FireMidge\ValueObject\Exception\ValueNotFound;
use PHPUnit\Framework\TestCase;

/**
 * Note: Read TransformIntArrayEnumTest for information on why we're saying that this test also covers BasicStringCollectionType.
 * It's the same reason as to why we're saying something similar there. (For Infection mutation test coverage.)
 *
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\BasicStringCollectionType
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
                [ new SimpleObject('E') ],
                'The following values are not valid: "E". Valid values are: "A", "B", "C", "D"'
            ],
            'a2' => [
                [ 'A', new SimpleObject('C')],
                'Invalid value. Must be an object and an instance of "FireMidge\Tests\ValueObject\Unit\Classes\SimpleObject"'
            ],
            'd2' => [
                [ new SimpleObject('C'), new SimpleObject('B'), new SimpleObject('E') ],
                'The following values are not valid: "E". Valid values are: "A", "B", "C", "D"'
            ],
            'x' => [
                [new SimpleObject('B'), new SimpleObject('X'), new SimpleObject('B')],
                'The following values are not valid: "X". Valid values are: "A", "B", "C", "D"'
            ],
        ];
    }

    /**
     * @dataProvider invalidValueProvider
     */
    public function testFromArrayWithInvalidValue(array $values, string $expectedExceptionMessage) : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        ObjectArrayEnumType::fromArray($values);
    }

    public function testWithAll() : void
    {
        $instance = ObjectArrayEnumType::withAll();
        $this->assertEquals([
            new SimpleObject('A'),
            new SimpleObject('B'),
            new SimpleObject('C'),
            new SimpleObject('D'),
        ], $instance->toArray());
    }

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
            'd' => [ new SimpleObject('D') ],
        ];
    }

    /**
     * @dataProvider singleValidValueProvider
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
                new SimpleObject('E'),
                'The following values are not valid: "E". Valid values are: "A", "B", "C", "D"'
            ],
        ];
    }

    /**
     * @dataProvider singleInvalidValueProvider
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

    public function testWithoutValueNotThrowingExceptionIfValueDidNotExist() : void
    {
        $instance = ObjectArrayEnumType::fromArray([new SimpleObject('B')]);
        $newInstance = $instance->withoutValue(new SimpleObject('A'));

        $this->assertEquals([new SimpleObject('B')], $newInstance->toArray());
    }

    public function testWithoutValueThrowingExceptionIfValueInvalid() : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('Invalid value. Must be an object and an instance of "FireMidge\Tests\ValueObject\Unit\Classes\SimpleObject"');

        $instance = ObjectArrayEnumType::fromArray([new SimpleObject('B')]);
        $instance->withoutValue('B');
    }

    public function testWithValueClonesValue() : void
    {
        $objectInside = new SimpleObject('A');
        $oldInstance = ObjectArrayEnumType::fromArray([$objectInside]);
        $newInstance = $oldInstance->withValue(new SimpleObject('B'));

        $objectInside->updateValue('C');

        $this->assertEquals(
            [new SimpleObject('C')],
            $oldInstance->toArray(),
            'Expected old instance to keep a reference to the original object'
        );

        $this->assertEquals(
            [new SimpleObject('A'), new SimpleObject('B')],
            $newInstance->toArray(),
            'Expected new instance to have cloned the previous contained object before returning, hence not keeping the reference to the original object'
        );
    }

    public function testWithoutValueClonesValue() : void
    {
        $objectInside = new SimpleObject('A');
        $oldInstance = ObjectArrayEnumType::fromArray([$objectInside, new SimpleObject('B')]);
        $newInstance = $oldInstance->withoutValue(new SimpleObject('B'));

        $objectInside->updateValue('C');

        $this->assertEquals(
            [new SimpleObject('C'), new SimpleObject('B')],
            $oldInstance->toArray(),
            'Expected old instance to keep a reference to the original object'
        );

        $this->assertEquals(
            [new SimpleObject('A')],
            $newInstance->toArray(),
            'Expected new instance to have cloned the previous contained object before returning, hence not keeping the reference to the original object'
        );
    }

    public function testTryWithoutValueClonesValue() : void
    {
        $objectInside = new SimpleObject('A');
        $oldInstance = ObjectArrayEnumType::fromArray([$objectInside, new SimpleObject('B')]);
        $newInstance = $oldInstance->tryWithoutValue(new SimpleObject('B'));

        $objectInside->updateValue('C');

        $this->assertEquals(
            [new SimpleObject('C'), new SimpleObject('B')],
            $oldInstance->toArray(),
            'Expected old instance to keep a reference to the original object'
        );

        $this->assertEquals(
            [new SimpleObject('A')],
            $newInstance->toArray(),
            'Expected new instance to have cloned the previous contained object before returning, hence not keeping the reference to the original object'
        );
    }

    public function testWithValuesClonesValue() : void
    {
        $objectInsideOld = new SimpleObject('A');
        $objectInsideNew = new SimpleObject('B');
        $oldInstance = ObjectArrayEnumType::fromArray([$objectInsideOld]);
        $newInstance = $oldInstance->withValues([new SimpleObject('B'), $objectInsideNew]);

        $objectInsideOld->updateValue('C');
        $objectInsideNew->updateValue('D');

        $this->assertEquals(
            [new SimpleObject('C')],
            $oldInstance->toArray(),
            'Expected old instance to keep a reference to the original object'
        );

        $this->assertEquals(
            [new SimpleObject('A'), new SimpleObject('B'), new SimpleObject('D')],
            $newInstance->toArray(),
            'Expected new instance to have cloned the previous contained object before returning, hence not keeping the reference to the original object. But it is supposed to keep a reference to the current new object (B->D)'
        );
    }

    public function testWithoutValuesClonesValue() : void
    {
        $objectInsideOld = new SimpleObject('A');
        $oldInstance = ObjectArrayEnumType::fromArray([
            $objectInsideOld,
            new SimpleObject('B'),
            new SimpleObject('C')
        ]);
        $newInstance = $oldInstance->withoutValues([
            new SimpleObject('B'),
            new SimpleObject('C')
        ]);

        $objectInsideOld->updateValue('D');

        $this->assertEquals(
            [new SimpleObject('D'), new SimpleObject('B'), new SimpleObject('C')],
            $oldInstance->toArray(),
            'Expected old instance to keep a reference to the original object'
        );

        $this->assertEquals(
            [new SimpleObject('A')],
            $newInstance->toArray(),
            'Expected new instance to have cloned the previous contained object before returning, hence not keeping the reference to the original object.'
        );
    }

    public function testTryWithoutValuesClonesValue() : void
    {
        $objectInsideOld = new SimpleObject('A');
        $oldInstance = ObjectArrayEnumType::fromArray([
            $objectInsideOld,
            new SimpleObject('B'),
            new SimpleObject('C')
        ]);
        $newInstance = $oldInstance->tryWithoutValues([
            new SimpleObject('B'),
            new SimpleObject('C')
        ]);

        $objectInsideOld->updateValue('D');

        $this->assertEquals(
            [new SimpleObject('D'), new SimpleObject('B'), new SimpleObject('C')],
            $oldInstance->toArray(),
            'Expected old instance to keep a reference to the original object'
        );

        $this->assertEquals(
            [new SimpleObject('A')],
            $newInstance->toArray(),
            'Expected new instance to have cloned the previous contained object before returning, hence not keeping the reference to the original object.'
        );
    }

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

        $this->assertTrue($instance1->isEqualTo($instance2, false));
        $this->assertFalse($instance1->isNotEqualTo($instance2, false));

        $this->assertTrue($instance1->isEqualTo($instance2), 'isEqualTo with strict check');
        $this->assertFalse($instance2->isNotEqualTo($instance1), 'isNotEqualTo with strict check');
    }

    public function testIsEqualWithArraySuccessful() : void
    {
        $instance = ObjectArrayEnumType::fromArray([
            new SimpleObject('A'),
            new SimpleObject('B'),
        ]);

        $array = ['A', 'B'];

        $this->assertTrue($instance->isEqualTo($array, false));
        $this->assertFalse($instance->isNotEqualTo($array, false));

        $this->assertFalse($instance->isEqualTo($array), 'isEqualTo with strict check');
        $this->assertTrue($instance->isNotEqualTo($array), 'isNotEqualTo with strict check');
    }
}