<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\CollectionType;

use FireMidge\Tests\ValueObject\Unit\Classes\SimpleObject;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleStringType;
use FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType;
use FireMidge\ValueObject\Exception\InvalidValue;
use FireMidge\ValueObject\Exception\ValueNotFound;
use PHPUnit\Framework\TestCase;
use stdClass;

class StringClassCollectionTest extends TestCase
{
    public function validValueProvider() : array
    {
        return [
            [ [], [] ],
            [
                [ SimpleStringType::fromString('Hello') ],
                [ SimpleStringType::fromString('Hello') ]
            ],
            [
                [ SimpleStringType::fromString('Hello'), SimpleStringType::fromString('World') ],
                [ SimpleStringType::fromString('Hello'), SimpleStringType::fromString('World') ],
            ],
            [
                [ SimpleStringType::fromString('Hello'), SimpleStringType::fromString('hello') ],
                [ SimpleStringType::fromString('Hello'), SimpleStringType::fromString('hello') ],
            ],
        ];
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::fromArray
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::toArray
     */
    public function testFromArrayWithValidValue(array $input, array $output) : void
    {
        $instance = StringClassCollectionType::fromArray($input);
        $this->assertEquals($output, $instance->toArray());
    }

    public function invalidValueProvider() : array
    {
        return [
            [ [ 1 ], 'Invalid value. Must be of type "object" but got "integer"' ],
            [ [ 1, 12.5 ], 'Invalid value. Must be of type "object" but got "integer"' ],
            [ [ new SimpleObject('Lorem') ], 'Invalid value. Must be an instance of "FireMidge\Tests\ValueObject\Unit\Classes\SimpleStringType", '
                . 'but is "FireMidge\Tests\ValueObject\Unit\Classes\SimpleObject"' ],
            [
                [
                    SimpleStringType::fromString('Hello'),
                    SimpleStringType::fromString('World'),
                    true,
                ],
                'Invalid value. Must be of type "object" but got "boolean"',
            ],
            [
                [
                    SimpleStringType::fromString('Hello'),
                    SimpleStringType::fromString('World'),
                    [ SimpleStringType::fromString('Moi') ],
                ],
                'Invalid value. Must be of type "object" but got "array"',
            ],
        ];
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::fromArray
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::toArray
     */
    public function testFromArrayWithInvalidValue(array $input, string $errorMessage) : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($errorMessage);

        StringClassCollectionType::fromArray($input);
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::contains
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::fromArray
     */
    public function testContainsWithEmptyArray() : void
    {
        $instance = StringClassCollectionType::fromArray([]);
        $this->assertFalse($instance->contains(SimpleStringType::fromString('World')));
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::contains
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::fromArray
     */
    public function testContainsIsCaseSensitive() : void
    {
        $instance = StringClassCollectionType::fromArray([
            SimpleStringType::fromString('Hello'),
            SimpleStringType::fromString('World'),
        ]);

        $this->assertTrue($instance->contains(SimpleStringType::fromString('Hello')), 'Expected to contain "Hello"');
        $this->assertTrue($instance->contains(SimpleStringType::fromString('World')), 'Expected to contain "World"');

        $this->assertFalse($instance->contains(SimpleStringType::fromString('hello')), 'Did not expect "hello" to be contained');
        $this->assertFalse($instance->contains(SimpleStringType::fromString('WORLD')), 'Did not expect "WORLD" to be contained');
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::contains
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::fromArray
     */
    public function testContainsThrowsExceptionWithInvalidType() : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('Invalid value. Must be of type "object" but got "integer"');

        $instance = StringClassCollectionType::fromArray([
            SimpleStringType::fromString('Hello'),
            SimpleStringType::fromString('World'),
        ]);

        $this->assertFalse($instance->contains(50));
    }

    public function singleValidValueProvider() : array
    {
        return [
            'name'   => [SimpleStringType::fromString('name')],
            'email'  => [SimpleStringType::fromString('email')],
            'status' => [SimpleStringType::fromString('status')],
        ];
    }

    /**
     * @dataProvider singleValidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::withValue
     */
    public function testWithValueWithValidValue(SimpleStringType $value) : void
    {
        $instance    = StringClassCollectionType::fromArray([
            SimpleStringType::fromString('Hello'),
            SimpleStringType::fromString('World'),
        ]);
        $newInstance = $instance->withValue($value);

        $this->assertEquals([
            SimpleStringType::fromString('Hello'),
            SimpleStringType::fromString('World'),
            $value
        ], $newInstance->toArray(), 'Expected new instance to match');
        $this->assertEquals([
            SimpleStringType::fromString('Hello'),
            SimpleStringType::fromString('World'),
        ], $instance->toArray(), 'Expected old instance to have remained unchanged');
    }

    public function singleInvalidValueProvider() : array
    {
        return [
            'int'       => [1, 'Invalid value. Must be of type "object" but got "integer"'],
            'bool'      => [false, 'Invalid value. Must be of type "object" but got "boolean"'],
            'string'    => ['hello', 'Invalid value. Must be of type "object" but got "string"'],
            'object'    => [
                new SimpleObject('name'),
                'Invalid value. Must be an instance of "FireMidge\Tests\ValueObject\Unit\Classes\SimpleStringType", '
                    . 'but is "FireMidge\Tests\ValueObject\Unit\Classes\SimpleObject"'],
        ];
    }

    /**
     * @dataProvider singleInvalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::withValue
     */
    public function testWithValueWithInvalidValue($invalidValue, string $expectedExceptionMessage) : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $instance = StringClassCollectionType::fromArray([
            SimpleStringType::fromString('Hello'),
        ]);
        $instance->withValue($invalidValue);
    }

    public function invalidWithoutValueProvider() : array
    {
        return [
            'invalidOne' => [
                [ SimpleStringType::fromString('email'), ],
                new SimpleObject('email'),
                InvalidValue::class,
                'Invalid value. Must be an instance of "FireMidge\Tests\ValueObject\Unit\Classes\SimpleStringType", '
                . 'but is "FireMidge\Tests\ValueObject\Unit\Classes\SimpleObject"',
            ],
            'invalidTwo' => [
                [ SimpleStringType::fromString('email'), ],
                1,
                InvalidValue::class,
                'Invalid value. Must be of type "object" but got "integer"',
            ],
            'invalidThree' => [
                [ SimpleStringType::fromString('status'), ],
                SimpleStringType::fromString('something'),
                ValueNotFound::class,
                'Value "something" was not found. Available values: "status"'
            ],
            'invalidFour' => [
                [ SimpleStringType::fromString('Hello'), SimpleStringType::fromString('World') ],
                SimpleStringType::fromString('something'),
                ValueNotFound::class,
                'Value "something" was not found. Available values: "Hello", "World"'
            ],
            'invalidFive' => [
                [ SimpleStringType::fromString('Hello'), SimpleStringType::fromString('World') ],
                SimpleStringType::fromString('hello'),
                ValueNotFound::class,
                'Value "hello" was not found. Available values: "Hello", "World"'
            ],
        ];
    }

    /**
     * @dataProvider invalidWithoutValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::tryWithoutValue
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

        $instance = StringClassCollectionType::fromArray($stateBefore);
        $instance->tryWithoutValue($valueToBeRemoved);
    }

    public function withoutValidValueProvider() : array
    {
        return [
            'one' => [
                [
                    SimpleStringType::fromString('Hello'),
                    SimpleStringType::fromString('World'),
                    SimpleStringType::fromString('Moi'),
                ],
                SimpleStringType::fromString('World'),
                [
                    SimpleStringType::fromString('Hello'),
                    SimpleStringType::fromString('Moi'),
                ],
            ],
            'two' => [
                [
                    SimpleStringType::fromString('Hello'),
                    SimpleStringType::fromString('World'),
                    SimpleStringType::fromString('Moi'),
                ],
                SimpleStringType::fromString('Hello'),
                [
                    SimpleStringType::fromString('World'),
                    SimpleStringType::fromString('Moi'),
                ],
            ],
            'three' => [
                [
                    SimpleStringType::fromString('Hello'),
                    SimpleStringType::fromString('World'),
                    SimpleStringType::fromString('Moi'),
                ],
                SimpleStringType::fromString('Moi'),
                [
                    SimpleStringType::fromString('Hello'),
                    SimpleStringType::fromString('World'),
                ],
            ],
            'four' => [
                [
                    SimpleStringType::fromString('Hello'),
                ],
                SimpleStringType::fromString('Hello'),
                [ ],
            ],
            'five' => [
                [
                    SimpleStringType::fromString('Hello'),
                    SimpleStringType::fromString('World'),
                ],
                SimpleStringType::fromString('Hello'),
                [
                    SimpleStringType::fromString('World'),
                ],
            ],
            'six' => [
                [
                    SimpleStringType::fromString('Hello'),
                    SimpleStringType::fromString('hello'),
                    SimpleStringType::fromString('World'),
                ],
                SimpleStringType::fromString('hello'),
                [
                    SimpleStringType::fromString('Hello'),
                    SimpleStringType::fromString('World'),
                ],
            ],
        ];
    }

    /**
     * @dataProvider withoutValidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::tryWithoutValue
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::toArray
     */
    public function testTryWithoutValueDoesNotChangePreExisting(
        array $stateBefore,
        SimpleStringType $valueToBeRemoved,
        array $stateAfter
    ) : void
    {
        $instance = StringClassCollectionType::fromArray($stateBefore);
        $newInstance = $instance->tryWithoutValue($valueToBeRemoved);

        $this->assertEquals($stateAfter, $newInstance->toArray(), 'Expected new instance to match');
        $this->assertEquals($stateBefore, $instance->toArray(), 'Expected old instance to have remained unchanged');
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::withoutValue
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::toArray
     */
    public function testWithoutValueNotThrowingExceptionIfValueDidNotExist() : void
    {
        $instance = StringClassCollectionType::fromArray(
            [
                SimpleStringType::fromString('World'),
            ],
        );
        $newInstance = $instance->withoutValue(SimpleStringType::fromString('world'));

        $this->assertEquals([SimpleStringType::fromString('World')], $newInstance->toArray());
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::withoutValue
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::toArray
     */
    public function testWithoutValueThrowingExceptionIfValueInvalid() : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage(
            'Invalid value. Must be an instance of "FireMidge\Tests\ValueObject\Unit\Classes\SimpleStringType", '
            . 'but is "FireMidge\Tests\ValueObject\Unit\Classes\SimpleObject"'
        );

        $instance = StringClassCollectionType::fromArray([
            SimpleStringType::fromString('Name')
        ]);
        $instance->withoutValue(new SimpleObject('Name'));
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::isEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::isNotEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::fromArray
     */
    public function testIsEqualToInstanceOfSameClassSuccessful() : void
    {
        $instance1 = StringClassCollectionType::fromArray(
            [
                SimpleStringType::fromString('Hello'),
                SimpleStringType::fromString('World'),
            ],
        );
        $instance2 = StringClassCollectionType::fromArray(
            [
                SimpleStringType::fromString('World'),
                SimpleStringType::fromString('Hello'),
            ],
        );

        $this->assertTrue($instance1->isEqualTo($instance2), 'Expected instance1 to be equal to instance2');
        $this->assertTrue($instance2->isEqualTo($instance1), 'Expected instance2 to be equal to instance1');
        $this->assertFalse($instance1->isNotEqualTo($instance2), 'Expected notEqualTo to return false for instance1');
        $this->assertFalse($instance2->isNotEqualTo($instance1), 'Expected notEqualTo to return false for instance2');
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::isEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::isNotEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::fromArray
     */
    public function testIsEqualToArraySuccessful() : void
    {
        $instance = StringClassCollectionType::fromArray(
            [
                SimpleStringType::fromString('Hello'),
                SimpleStringType::fromString('World'),
            ],
        );
        $array = [
            SimpleStringType::fromString('World'),
            SimpleStringType::fromString('Hello'),
        ];

        $this->assertTrue($instance->isEqualTo($array), 'Expected instance to be equal to array');
        $this->assertFalse($instance->isNotEqualTo($array), 'Expected isNotEqualTo to return false');
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::isEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::isNotEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::fromArray
     */
    public function testIsEqualToStandardObjectSuccessful() : void
    {
        $instance = StringClassCollectionType::fromArray(
            [
                SimpleStringType::fromString('Hello'),
                SimpleStringType::fromString('World'),
            ],
        );

        $object         = new stdClass();
        $object->first  = SimpleStringType::fromString('World');
        $object->second = SimpleStringType::fromString('Hello');

        $this->assertTrue($instance->isEqualTo($object), 'Expected instance to be equal to object');
        $this->assertFalse($instance->isNotEqualTo($object), 'Expected isNotEqualTo to return false');
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::count
     */
    public function testCount() : void
    {
        $instance = StringClassCollectionType::fromArray(
            [
                SimpleStringType::fromString('Hello'),
                SimpleStringType::fromString('World'),
                SimpleStringType::fromString('hello'),
            ],
        );

        $this->assertSame(3, $instance->count());
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::empty
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::count
     */
    public function testEmptyFactoryMethod() : void
    {
        $instance = StringClassCollectionType::empty();

        $this->assertCount(0, $instance->toArray());
        $this->assertSame(0, $instance->count());
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassCollectionType::toStringArray
     */
    public function testToStringArray() : void
    {
        $instance = StringClassCollectionType::fromArray(
            [
                SimpleStringType::fromString('Hello'),
                SimpleStringType::fromString('World'),
                SimpleStringType::fromString('hello'),
            ],
        );

        $this->assertEquals([
            'Hello',
            'World',
            'hello',
        ], $instance->toStringArray());
    }
}