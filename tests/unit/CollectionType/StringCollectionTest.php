<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\CollectionType;

use FireMidge\Tests\ValueObject\Unit\Classes\SimpleObject;
use FireMidge\Tests\ValueObject\Unit\Classes\StringArrayEnumUpperCaseType;
use FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionOriginalCasingType;
use FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType;
use FireMidge\Tests\ValueObject\Unit\Classes\StringEnumType;
use FireMidge\Tests\ValueObject\Unit\Classes\StringVOArrayEnumType;
use FireMidge\ValueObject\Exception\InvalidValue;
use FireMidge\ValueObject\Exception\ValueNotFound;
use PHPUnit\Framework\TestCase;
use stdClass;

class StringCollectionTest extends TestCase
{
    public function validValueProvider() : array
    {
        return [
            [ [], [] ],
            [ [ '   name' ], [ 'Name' ] ],
            [ [ 'email  ' ], [ 'Email' ] ],
            [ [ ' STATUS' ], [ 'Status' ] ],
            [ [ 'any', 'string', 'goes' ], ['Any', 'String', 'Goes']],
            [ [ '1', '23.45', '@!()' ], [ '1', '23.45', '@!()' ]]
        ];
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::fromArray
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::toArray
     */
    public function testFromArrayWithValidValue(array $input, array $output) : void
    {
        $instance = StringCollectionType::fromArray($input);
        $this->assertSame($output, $instance->toArray());
    }

    public function invalidValueProvider() : array
    {
        return [
            [ [ 'two', 'three', 'two' ], 'Values contain duplicates. Only unique values allowed. Values passed: "Two", "Three", "Two"' ],
            [ [ '  TWO', 'three', ' tWo' ], 'Values contain duplicates. Only unique values allowed. Values passed: "Two", "Three", "Two"' ],
            [ [ 1 ], 'Invalid value. Must be of type "string" but got "integer"' ],
            [ [ 1, 12.5 ], 'Invalid value. Must be of type "string" but got "integer"' ],
            [ [ new SimpleObject('Lorem') ], 'Invalid value. Must be of type "string" but got "object"' ],
            [ [ '  TWO', 'three', true ], 'Invalid value. Must be of type "string" but got "boolean"' ],
            [ [ '  TWO', 'three', [ 'four' ] ], 'Invalid value. Must be of type "string" but got "array"' ],
        ];
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::fromArray
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::toArray
     */
    public function testFromArrayWithInvalidValue(array $input, string $errorMessage) : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($errorMessage);

        StringCollectionType::fromArray($input);
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::contains
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::fromArray
     */
    public function testContainsWithEmptyArray() : void
    {
        $instance = StringCollectionType::fromArray([]);
        $this->assertFalse($instance->contains('ipsum'));
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::contains
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::fromArray
     */
    public function testContainsIsCaseSensitive() : void
    {
        $instance = StringCollectionType::fromArray([
            'lorem',
            ' ipsum',
            'SIT'
        ]);

        $this->assertTrue($instance->contains('Lorem'), 'Expected to contain "Lorem"');
        $this->assertTrue($instance->contains('Ipsum'), 'Expected to contain "Ipsum"');
        $this->assertTrue($instance->contains('Sit'), 'Expected to contain "Sit"');

        $this->assertFalse($instance->contains('SIT'), 'Did not expect "SIT" to be contained');
        $this->assertFalse($instance->contains('lorem'), 'Did not expect "lorem" to be contained');
        $this->assertFalse($instance->contains(' ipsum'), 'Did not expect " ipsum" to be contained');
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::contains
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::fromArray
     */
    public function testContainsThrowsExceptionWithInvalidType() : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('Invalid value. Must be of type "string" but got "integer"');

        $instance = StringCollectionType::fromArray([
            'green',
            'yellow'
        ]);

        $this->assertFalse($instance->contains(50));
    }

    public function singleValidValueProvider() : array
    {
        return [
            'name'   => ['Name'],
            'email'  => ['Email'],
            'status' => ['Status'],
        ];
    }

    /**
     * @dataProvider singleValidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::withValue
     */
    public function testWithValueWithValidValue(string $value) : void
    {
        $instance    = StringCollectionType::fromArray([
            'address',
            'phone number',
        ]);
        $newInstance = $instance->withValue($value);

        $this->assertEquals([
            'Address',
            'Phone number',
            $value
        ], $newInstance->toArray(), 'Expected new instance to match');
        $this->assertEquals([
            'Address',
            'Phone number',
        ], $instance->toArray(), 'Expected old instance to have remained unchanged');
    }

    public function singleInvalidValueProvider() : array
    {
        return [
            'duplicate' => ['SOME-VALUE', 'Values contain duplicates. Only unique values allowed. Values passed: "Some-value", "Some-value"'],
            'int'       => [1, 'Invalid value. Must be of type "string" but got "integer"'],
            'bool'      => [false, 'Invalid value. Must be of type "string" but got "boolean"'],
            'object'    => [new SimpleObject('name'), 'Invalid value. Must be of type "string" but got "object"'],
        ];
    }

    /**
     * @dataProvider singleInvalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::withValue
     */
    public function testWithValueWithInvalidValue($invalidValue, string $expectedExceptionMessage) : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $instance = StringCollectionType::fromArray([
            'some-value'
        ]);
        $instance->withValue($invalidValue);
    }

    public function invalidWithoutValueProvider() : array
    {
        return [
            'invalidOne' => [
                [ 'email' ],
                new SimpleObject('email'),
                InvalidValue::class,
                'Invalid value. Must be of type "string" but got "object"',
            ],
            'invalidTwo' => [
                [ 'email' ],
                1,
                InvalidValue::class,
                'Invalid value. Must be of type "string" but got "integer"',
            ],
            'invalidThree' => [
                [ 'status' ],
                'something',
                ValueNotFound::class,
                'Value "something" was not found. Available values: "Status"'
            ],
            'invalidFour' => [
                [ 'status', 'email', 'name' ],
                'something',
                ValueNotFound::class,
                'Value "something" was not found. Available values: "Status", "Email", "Name"'
            ],
            'invalidFive' => [
                [ 'status', 'email', 'name' ],
                'status',
                ValueNotFound::class,
                'Value "status" was not found. Available values: "Status", "Email", "Name"'
            ],
        ];
    }

    /**
     * @dataProvider invalidWithoutValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::tryWithoutValue
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

        $instance = StringCollectionType::fromArray($stateBefore);
        $instance->tryWithoutValue($valueToBeRemoved);
    }

    public function withoutValidValueProvider() : array
    {
        return [
            'one' => [
                [ 'Name', 'Email', 'Status' ],
                'Name',
                [ 'Email', 'Status' ],
            ],
            'two' => [
                [ 'Name', 'Phone', 'Email', 'Status' ],
                'Name',
                [ 'Phone', 'Email', 'Status' ],
            ],
            'three' => [
                [ 'Name', 'Email', 'Status', 'Phone' ],
                'Phone',
                [ 'Name', 'Email', 'Status' ],
            ],
            'four' => [
                [ 'Status' ],
                'Status',
                [ ],
            ],
            'five' => [
                [ 'Email', 'Status' ],
                'Email',
                [ 'Status' ],
            ],
        ];
    }

    /**
     * @dataProvider withoutValidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::tryWithoutValue
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::toArray
     */
    public function testTryWithoutValueDoesNotChangePreExisting(
        array $stateBefore,
        string $valueToBeRemoved,
        array $stateAfter
    ) : void
    {
        $instance = StringCollectionType::fromArray($stateBefore);
        $newInstance = $instance->tryWithoutValue($valueToBeRemoved);

        $this->assertEquals($stateAfter, $newInstance->toArray(), 'Expected new instance to match');
        $this->assertEquals($stateBefore, $instance->toArray(), 'Expected old instance to have remained unchanged');
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::withoutValue
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::toArray
     */
    public function testWithoutValueNotThrowingExceptionIfValueDidNotExist() : void
    {
        $instance = StringCollectionType::fromArray(['Name']);
        $newInstance = $instance->withoutValue('Email');

        $this->assertEquals(['Name'], $newInstance->toArray());
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::withoutValue
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::toArray
     */
    public function testWithoutValueThrowingExceptionIfValueInvalid() : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('Invalid value. Must be of type "string" but got "object"');

        $instance = StringCollectionType::fromArray(['Name']);
        $instance->withoutValue(new SimpleObject('Name'));
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::isEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::isNotEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::fromArray
     */
    public function testIsEqualToInstanceOfSameClassSuccessful() : void
    {
        $instance1 = StringCollectionType::fromArray(['Name', 'Status']);
        $instance2 = StringCollectionType::fromArray(['Status', 'Name']);

        $this->assertTrue($instance1->isEqualTo($instance2), 'Expected instance1 to be equal to instance2');
        $this->assertTrue($instance2->isEqualTo($instance1), 'Expected instance2 to be equal to instance1');
        $this->assertFalse($instance1->isNotEqualTo($instance2), 'Expected notEqualTo to return false for instance1');
        $this->assertFalse($instance2->isNotEqualTo($instance1), 'Expected notEqualTo to return false for instance2');
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionOriginalCasingType::isEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionOriginalCasingType::isNotEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionOriginalCasingType::fromArray
     */
    public function testIsEqualToInstanceOfDifferentTypesSuccessful() : void
    {
        $instance1 = StringVOArrayEnumType::fromArray([
            StringEnumType::spring(),
            StringEnumType::winter(),
        ]);
        $instance2 = StringCollectionOriginalCasingType::fromArray(['spring', 'winter']);

        $this->assertTrue($instance1->isEqualTo($instance2), 'Expected instance1 to be equal to instance2');
        $this->assertTrue($instance2->isEqualTo($instance1), 'Expected instance2 to be equal to instance1');
        $this->assertFalse($instance1->isNotEqualTo($instance2), 'Expected notEqualTo to return false for instance1');
        $this->assertFalse($instance2->isNotEqualTo($instance1), 'Expected notEqualTo to return false for instance2');
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::isEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::isNotEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::fromArray
     */
    public function testIsEqualToInstanceOfDifferentClassSuccessful() : void
    {
        $instance1 = StringArrayEnumUpperCaseType::fromArray([
            'Email',
            'Status',
        ]);
        $instance2 = StringCollectionType::fromArray(['email', 'status']);

        $this->assertTrue($instance1->isEqualTo($instance2), 'Expected instance1 to be equal to instance2');
        $this->assertTrue($instance2->isEqualTo($instance1), 'Expected instance2 to be equal to instance1');
        $this->assertFalse($instance1->isNotEqualTo($instance2), 'Expected notEqualTo to return false for instance1');
        $this->assertFalse($instance2->isNotEqualTo($instance1), 'Expected notEqualTo to return false for instance2');
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::isEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::isNotEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::fromArray
     */
    public function testIsEqualToArraySuccessful() : void
    {
        $instance2 = StringCollectionType::fromArray(['Hello', 'H3llo', ' _Hello']);
        $array = [
            '_hello',
            'H3llo',
            'Hello',
        ];

        $this->assertTrue($instance2->isEqualTo($array), 'Expected instance1 to be equal to array');
        $this->assertFalse($instance2->isNotEqualTo($array), 'Expected isNotEqualTo to return false');
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::isEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::isNotEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::fromArray
     */
    public function testIsEqualToStandardObjectSuccessful() : void
    {
        $instance2      = StringCollectionType::fromArray(['Hello', 'H3llo', ' _Hello']);
        $object         = new stdClass();
        $object->first  = '_hello';
        $object->second = 'H3llo';
        $object->third  = 'Hello';

        $this->assertTrue($instance2->isEqualTo($object), 'Expected instance1 to be equal to object');
        $this->assertFalse($instance2->isNotEqualTo($object), 'Expected isNotEqualTo to return false');
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::isEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::isNotEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::fromArray
     */
    public function testIsEqualToInstanceOfSameClassNotEqual() : void
    {
        $instance1 = StringCollectionType::fromArray(['Name', 'Status']);
        $instance2 = StringCollectionType::fromArray(['Status']);

        $this->assertFalse($instance1->isEqualTo($instance2), 'Expected instance1 not to be equal to instance2');
        $this->assertTrue($instance1->isNotEqualTo($instance2), 'Expected instance1 not to be equal to instance2');
        $this->assertFalse($instance2->isEqualTo($instance1), 'Expected instance2 not to be equal to instance1');
        $this->assertTrue($instance2->isNotEqualTo($instance1), 'Expected instance2 not to be equal to instance1');
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::isEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::isNotEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::fromArray
     */
    public function testIsEqualToInstanceOfDifferentClassNotEqual() : void
    {
        $instance1 = StringArrayEnumUpperCaseType::fromArray(['Email',]);
        $instance2 = StringCollectionType::fromArray(['email', 'status']);

        $this->assertFalse($instance1->isEqualTo($instance2), 'Expected instance1 not to be equal to instance2');
        $this->assertTrue($instance1->isNotEqualTo($instance2), 'Expected instance1 not to be equal to instance2');
        $this->assertFalse($instance2->isEqualTo($instance1), 'Expected instance2 not to be equal to instance1');
        $this->assertTrue($instance2->isNotEqualTo($instance1), 'Expected instance2 not to be equal to instance1');
    }

    public function notEqualProvider() : array
    {
        return [
            'differentCount'  => [ ['Hello', 'H3llo'] ],
            'differentValues' => [ ['Hello', 'H3llo', ' Hello_'] ],
        ];
    }

    /**
     * @dataProvider notEqualProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::isEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::isNotEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::fromArray
     */
    public function testIsEqualToArrayNotEqual(array $valuesToCompareTo) : void
    {
        $instance2 = StringCollectionType::fromArray(['Hello', 'H3llo', ' _Hello']);

        $this->assertFalse($instance2->isEqualTo($valuesToCompareTo));
        $this->assertTrue($instance2->isNotEqualTo($valuesToCompareTo));
    }

    /**
     * @dataProvider notEqualProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::isEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::isNotEqualTo
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::fromArray
     */
    public function testIsEqualToStandardObjectNotEqual(array $valuesToCompareTo) : void
    {
        $instance2 = StringCollectionType::fromArray(['Hello', 'H3llo', ' _Hello']);

        $object = new stdClass();
        foreach ($valuesToCompareTo as $k => $v) {
            $propertyName          = 'property' . $k;
            $object->$propertyName = $v;
        }

        $this->assertFalse($instance2->isEqualTo($object));
        $this->assertTrue($instance2->isNotEqualTo($object));
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::count
     */
    public function testCount() : void
    {
        $instance = StringCollectionType::fromArray(['Hello', 'H3llo', ' _Hello']);

        $this->assertSame(3, $instance->count());
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::empty
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::count
     */
    public function testEmptyFactoryMethod() : void
    {
        $instance = StringCollectionType::empty();

        $this->assertCount(0, $instance->toArray());
        $this->assertSame(0, $instance->count());
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::isEmpty
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::isNotEmpty
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::empty
     */
    public function testIsEmptyIsTrue() : void
    {
        $instance = StringCollectionType::empty();
        $this->assertTrue($instance->isEmpty());
        $this->assertFalse($instance->isNotEmpty());
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::isEmpty
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::isNotEmpty
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::fromArray
     */
    public function testIsEmptyIsFalse() : void
    {
        $instance = StringCollectionType::fromArray(['status']);
        $this->assertFalse($instance->isEmpty());
        $this->assertTrue($instance->isNotEmpty());
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::isEmpty
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::isNotEmpty
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::empty
     */
    public function testIsEmptyAfterAddingValue() : void
    {
        $instance = StringCollectionType::empty();
        $instance = $instance->withValue('newValue');

        $this->assertFalse($instance->isEmpty());
        $this->assertTrue($instance->isNotEmpty());
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::isEmpty
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::isNotEmpty
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::fromArray
     */
    public function testIsEmptyAfterRemovingValueWithTryWithoutValue() : void
    {
        $instance = StringCollectionType::fromArray(['status']);
        $instance = $instance->tryWithoutValue('Status');

        $this->assertTrue($instance->isEmpty());
        $this->assertFalse($instance->isNotEmpty());
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::isEmpty
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::isNotEmpty
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType::fromArray
     */
    public function testIsEmptyAfterRemovingValue() : void
    {
        $instance = StringCollectionType::fromArray(['status']);
        $instance = $instance->withoutValue('Status');

        $this->assertTrue($instance->isEmpty());
        $this->assertFalse($instance->isNotEmpty());
    }
}