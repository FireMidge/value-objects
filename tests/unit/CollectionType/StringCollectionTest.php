<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\CollectionType;

use FireMidge\Tests\ValueObject\Unit\Classes\SimpleObject;
use FireMidge\Tests\ValueObject\Unit\Classes\StringArrayEnumUpperCaseType;
use FireMidge\Tests\ValueObject\Unit\Classes\BasicStringCollectionType;
use FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType;
use FireMidge\Tests\ValueObject\Unit\Classes\StringEnumType;
use FireMidge\Tests\ValueObject\Unit\Classes\StringVOArrayEnumType;
use FireMidge\ValueObject\Exception\DuplicateValue;
use FireMidge\ValueObject\Exception\InvalidValue;
use FireMidge\ValueObject\Exception\ValueNotFound;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringCollectionType
 * @uses \FireMidge\Tests\ValueObject\Unit\Classes\SimpleStringType
 */
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
     */
    public function testFromArrayWithInvalidValue(array $input, string $errorMessage) : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($errorMessage);

        StringCollectionType::fromArray($input);
    }

    public function testContainsWithEmptyArray() : void
    {
        $instance = StringCollectionType::fromArray([]);
        $this->assertFalse($instance->contains('ipsum'));
    }

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
            'duplicate' => ['SOME-VALUE', 'Value "Some-value" cannot be used as it already exists within array. Existing values: "Some-value".'],
            'int'       => [1, 'Invalid value. Must be of type "string" but got "integer"'],
            'bool'      => [false, 'Invalid value. Must be of type "string" but got "boolean"'],
            'object'    => [new SimpleObject('name'), 'Invalid value. Must be of type "string" but got "object"'],
        ];
    }

    /**
     * @dataProvider singleInvalidValueProvider
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

    public function testWithoutValueNotThrowingExceptionIfValueDidNotExist() : void
    {
        $instance = StringCollectionType::fromArray(['Name']);
        $newInstance = $instance->withoutValue('Email');

        $this->assertEquals(['Name'], $newInstance->toArray());
    }

    public function testWithoutValueThrowingExceptionIfValueInvalid() : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('Invalid value. Must be of type "string" but got "object"');

        $instance = StringCollectionType::fromArray(['Name']);
        $instance->withoutValue(new SimpleObject('Name'));
    }

    public function testIsEqualToInstanceOfSameClassSuccessful() : void
    {
        $instance1 = StringCollectionType::fromArray(['Name', 'Status']);
        $instance2 = StringCollectionType::fromArray(['Status', 'Name']);

        $this->assertTrue(
            $instance1->isEqualTo($instance2, false),
            'Expected instance1 to be equal to instance2'
        );
        $this->assertTrue(
            $instance2->isEqualTo($instance1, false),
            'Expected instance2 to be equal to instance1'
        );
        $this->assertFalse(
            $instance1->isNotEqualTo($instance2, false),
            'Expected notEqualTo to return false for instance1'
        );
        $this->assertFalse(
            $instance2->isNotEqualTo($instance1, false),
            'Expected notEqualTo to return false for instance2'
        );

        $this->assertTrue(
            $instance1->isEqualTo($instance2),
            'Expected instance1 to be equal to instance2 with strict check'
        );
        $this->assertTrue(
            $instance2->isEqualTo($instance1),
            'Expected instance2 to be equal to instance1 with strict check'
        );
        $this->assertFalse(
            $instance1->isNotEqualTo($instance2),
            'Expected notEqualTo to return false for instance1 with strict check'
        );
        $this->assertFalse(
            $instance2->isNotEqualTo($instance1),
            'Expected notEqualTo to return false for instance2 with strict check'
        );
    }

    public function testIsEqualToInstanceOfDifferentTypesSuccessful() : void
    {
        $instance1 = StringVOArrayEnumType::fromArray([
            StringEnumType::spring(),
            StringEnumType::winter(),
        ]);
        $instance2 = BasicStringCollectionType::fromArray(['spring', 'winter']);

        $this->assertTrue(
            $instance1->isEqualTo($instance2, false),
            'Expected instance1 to be equal to instance2'
        );
        $this->assertTrue(
            $instance2->isEqualTo($instance1, false),
            'Expected instance2 to be equal to instance1'
        );
        $this->assertFalse(
            $instance1->isNotEqualTo($instance2, false),
            'Expected notEqualTo to return false for instance1'
        );
        $this->assertFalse(
            $instance2->isNotEqualTo($instance1, false),
            'Expected notEqualTo to return false for instance2'
        );

        $this->assertFalse(
            $instance1->isEqualTo($instance2),
            'Expected instance1 not to be equal to instance2 with strict check'
        );
        $this->assertFalse(
            $instance2->isEqualTo($instance1),
            'Expected instance2 not to be equal to instance1 with strict check'
        );
        $this->assertTrue(
            $instance1->isNotEqualTo($instance2),
            'Expected notEqualTo to return true for instance1 with strict check'
        );
        $this->assertTrue(
            $instance2->isNotEqualTo($instance1),
            'Expected notEqualTo to return true for instance2 with strict check'
        );
    }

    public function testIsEqualToInstanceOfDifferentClassSuccessful() : void
    {
        $instance1 = StringArrayEnumUpperCaseType::fromArray([
            'Email',
            'Status',
        ]);
        $instance2 = StringCollectionType::fromArray(['email', 'status']);

        $this->assertTrue(
            $instance1->isEqualTo($instance2, false),
            'Expected instance1 to be equal to instance2'
        );
        $this->assertTrue(
            $instance2->isEqualTo($instance1, false),
            'Expected instance2 to be equal to instance1'
        );
        $this->assertFalse(
            $instance1->isNotEqualTo($instance2, false),
            'Expected notEqualTo to return false for instance1'
        );
        $this->assertFalse(
            $instance2->isNotEqualTo($instance1, false),
            'Expected notEqualTo to return false for instance2'
        );

        $this->assertFalse(
            $instance1->isEqualTo($instance2),
            'Expected instance1 not to be equal to instance2 with strict check'
        );
        $this->assertFalse(
            $instance2->isEqualTo($instance1),
            'Expected instance2 not to be equal to instance1 with strict check'
        );
        $this->assertTrue(
            $instance1->isNotEqualTo($instance2),
            'Expected notEqualTo to return true for instance1 with strict check'
        );
        $this->assertTrue(
            $instance2->isNotEqualTo($instance1),
            'Expected notEqualTo to return true for instance2 with strict check'
        );
    }

    public function testIsEqualToArraySuccessful() : void
    {
        $instance = StringCollectionType::fromArray(['Hello', 'H3llo', ' _Hello']);
        $array    = ['_hello', 'H3llo', 'Hello',];

        $this->assertTrue(
            $instance->isEqualTo($array, false),
            'Expected instance to be equal to array'
        );
        $this->assertFalse(
            $instance->isNotEqualTo($array, false),
            'Expected isNotEqualTo to return false'
        );

        $this->assertFalse(
            $instance->isEqualTo($array),
            'Expected instance not to be equal to array with strict check'
        );
        $this->assertTrue(
            $instance->isNotEqualTo($array),
            'Expected isNotEqualTo to return true with strict check'
        );
    }

    public function testIsEqualToStandardObjectSuccessful() : void
    {
        $instance       = StringCollectionType::fromArray(['Hello', 'H3llo', ' _Hello']);
        $object         = new stdClass();
        $object->first  = '_hello';
        $object->second = 'H3llo';
        $object->third  = 'Hello';

        $this->assertTrue(
            $instance->isEqualTo($object, false),
            'Expected instance to be equal to object'
        );
        $this->assertFalse(
            $instance->isNotEqualTo($object, false),
            'Expected isNotEqualTo to return false'
        );

        $this->assertFalse(
            $instance->isEqualTo($object),
            'Expected instance not to be equal to object with strict check'
        );
        $this->assertTrue(
            $instance->isNotEqualTo($object),
            'Expected isNotEqualTo to return true with strict check'
        );
    }

    public function testIsEqualToInstanceOfSameClassNotEqual() : void
    {
        $instance1 = StringCollectionType::fromArray(['Name', 'Status']);
        $instance2 = StringCollectionType::fromArray(['Status']);

        $this->assertFalse(
            $instance1->isEqualTo($instance2, false),
            'Expected instance1 not to be equal to instance2'
        );
        $this->assertFalse(
            $instance2->isEqualTo($instance1, false),
            'Expected instance2 not to be equal to instance1'
        );
        $this->assertTrue(
            $instance1->isNotEqualTo($instance2, false),
            'Expected isNotEqualTo to return true for instance1'
        );
        $this->assertTrue(
            $instance2->isNotEqualTo($instance1, false),
            'Expected isNotEqualTo to return true for instance2'
        );

        $this->assertFalse(
            $instance1->isEqualTo($instance2),
            'Expected instance1 not to be equal to instance2 with strict check'
        );
        $this->assertFalse(
            $instance2->isEqualTo($instance1),
            'Expected instance2 not to be equal to instance1 with strict check'
        );
        $this->assertTrue(
            $instance1->isNotEqualTo($instance2),
            'Expected isNotEqualTo to return true for instance1 with strict check'
        );
        $this->assertTrue(
            $instance2->isNotEqualTo($instance1),
            'Expected isNotEqualTo to return true for instance2 with strict check'
        );
    }

    public function testIsEqualToInstanceOfDifferentClassNotEqual() : void
    {
        $instance1 = StringArrayEnumUpperCaseType::fromArray(['Email',]);
        $instance2 = StringCollectionType::fromArray(['email', 'status']);

        $this->assertFalse(
            $instance1->isEqualTo($instance2, false),
            'Expected instance1 not to be equal to instance2'
        );
        $this->assertFalse(
            $instance2->isEqualTo($instance1, false),
            'Expected instance2 not to be equal to instance1'
        );
        $this->assertTrue(
            $instance1->isNotEqualTo($instance2, false),
            'Expected isNotEqualTo to return true for instance1'
        );
        $this->assertTrue(
            $instance2->isNotEqualTo($instance1, false),
            'Expected isNotEqualTo to return true for instance2'
        );

        $this->assertFalse(
            $instance1->isEqualTo($instance2),
            'Expected instance1 not to be equal to instance2 with strict check'
        );
        $this->assertFalse(
            $instance2->isEqualTo($instance1),
            'Expected instance2 not to be equal to instance1 with strict check'
        );
        $this->assertTrue(
            $instance1->isNotEqualTo($instance2),
            'Expected isNotEqualTo to return true for instance1 with strict check'
        );
        $this->assertTrue(
            $instance2->isNotEqualTo($instance1),
            'Expected isNotEqualTo to return true for instance2 with strict check'
        );
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
     */
    public function testIsEqualToArrayNotEqual(array $valuesToCompareTo) : void
    {
        $instance2 = StringCollectionType::fromArray(['Hello', 'H3llo', ' _Hello']);

        $this->assertFalse($instance2->isEqualTo($valuesToCompareTo, false));
        $this->assertTrue($instance2->isNotEqualTo($valuesToCompareTo, false));

        $this->assertFalse($instance2->isEqualTo($valuesToCompareTo), 'isEqualTo with strict check');
        $this->assertTrue($instance2->isNotEqualTo($valuesToCompareTo), 'isNotEqualTo with strict check');
    }

    /**
     * @dataProvider notEqualProvider
     */
    public function testIsEqualToStandardObjectNotEqual(array $valuesToCompareTo) : void
    {
        $instance = StringCollectionType::fromArray(['Hello', 'H3llo', ' _Hello']);

        $object = new stdClass();
        foreach ($valuesToCompareTo as $k => $v) {
            $propertyName          = 'property' . $k;
            $object->$propertyName = $v;
        }

        $this->assertFalse($instance->isEqualTo($object, false));
        $this->assertTrue($instance->isNotEqualTo($object, false));

        $this->assertFalse($instance->isEqualTo($object), 'isEqualTo with strict check');
        $this->assertTrue($instance->isNotEqualTo($object), 'isNotEqualTo with strict check');
    }

    public function testEqualWithNullReturnsFalse() : void
    {
        $instance = StringCollectionType::fromArray(['Hello', 'H3llo', ' _Hello']);

        $this->assertFalse($instance->isEqualTo(null, false));
        $this->assertTrue($instance->isNotEqualTo(null, false));

        $this->assertFalse($instance->isEqualTo(null), 'isEqualTo with strict check');
        $this->assertTrue($instance->isNotEqualTo(null), 'isNotEqualTo with strict check');
    }

    public function testEqualWithNullReturnsFalseWhenComparingWithEmpty() : void
    {
        $instance = StringCollectionType::empty();

        $this->assertFalse($instance->isEqualTo(null, false));
        $this->assertTrue($instance->isNotEqualTo(null, false));

        $this->assertFalse($instance->isEqualTo(null), 'isEqualTo with strict check');
        $this->assertTrue($instance->isNotEqualTo(null), 'isNotEqualTo with strict check');
    }

    public function testCount() : void
    {
        $instance = StringCollectionType::fromArray(['Hello', 'H3llo', ' _Hello']);

        $this->assertSame(3, $instance->count());
    }

    public function testEmptyFactoryMethod() : void
    {
        $instance = StringCollectionType::empty();

        $this->assertCount(0, $instance->toArray());
        $this->assertSame(0, $instance->count());
    }

    public function testIsEmptyIsTrue() : void
    {
        $instance = StringCollectionType::empty();
        $this->assertTrue($instance->isEmpty());
        $this->assertFalse($instance->isNotEmpty());
    }

    public function testIsEmptyIsFalse() : void
    {
        $instance = StringCollectionType::fromArray(['status']);
        $this->assertFalse($instance->isEmpty());
        $this->assertTrue($instance->isNotEmpty());
    }

    public function testIsEmptyAfterAddingValue() : void
    {
        $instance = StringCollectionType::empty();
        $instance = $instance->withValue('newValue');

        $this->assertFalse($instance->isEmpty());
        $this->assertTrue($instance->isNotEmpty());
    }

    public function testIsEmptyAfterRemovingValueWithTryWithoutValue() : void
    {
        $instance = StringCollectionType::fromArray(['status']);
        $instance = $instance->tryWithoutValue('Status');

        $this->assertTrue($instance->isEmpty());
        $this->assertFalse($instance->isNotEmpty());
    }

    public function testIsEmptyAfterRemovingValue() : void
    {
        $instance = StringCollectionType::fromArray(['status']);
        $instance = $instance->withoutValue('Status');

        $this->assertTrue($instance->isEmpty());
        $this->assertFalse($instance->isNotEmpty());
    }

    public function testAddValuesBeingSuccessful() : void
    {
        $instance  = StringCollectionType::fromArray(['Orange', 'Green']);
        $instance2 = $instance->withValues(['Red', 'Black', 'Purple']);

        $this->assertEquals([
            'Orange',
            'Green',
            'Red',
            'Black',
            'Purple',
        ], $instance2->toArray(), 'Expected new array to match');
        $this->assertEquals([
            'Orange',
            'Green',
        ], $instance->toArray(), 'Expected original to have remained untouched');
    }

    public function testAddValuesWithAnEmptyArray() : void
    {
        $instance  = StringCollectionType::fromArray(['Orange', 'Green']);
        $instance2 = $instance->withValues([]);

        $this->assertEquals([
            'Orange',
            'Green',
        ], $instance2->toArray(), 'Expected new array to match');
        $this->assertEquals([
            'Orange',
            'Green',
        ], $instance->toArray(), 'Expected original to have remained untouched');
    }

    public function testAddValuesThrowingExceptionBecauseOfDuplicateValue() : void
    {
        $this->expectException(DuplicateValue::class);
        $this->expectExceptionMessage(
            'Value "Green" cannot be used as it already exists within array. '
            . 'Existing values: "Orange", "Green", "Red", "Black"'
        );

        $instance = StringCollectionType::fromArray(['Orange', 'Green']);
        $instance->withValues(['Red', 'Black', 'Green']);
    }

    public function testWithoutValuesBeingSuccessful() : void
    {
        $instance  = StringCollectionType::fromArray(['Orange', 'Black', 'Green', 'Purple']);
        $instance2 = $instance->withoutValues(['Red', 'Black', 'Purple', 'Black']);

        $this->assertEquals([
            'Orange',
            'Green',
        ], $instance2->toArray(), 'Expected new array to match');
        $this->assertEquals([
            'Orange',
            'Black',
            'Green',
            'Purple'
        ], $instance->toArray(), 'Expected original to have remained untouched');
    }

    public function testWithoutValuesWithEmptyArray() : void
    {
        $instance  = StringCollectionType::fromArray(['Orange', 'Black', 'Green', 'Purple']);
        $instance2 = $instance->withoutValues([]);

        $this->assertEquals([
            'Orange',
            'Black',
            'Green',
            'Purple'
        ], $instance2->toArray(), 'Expected new array to match');
        $this->assertEquals([
            'Orange',
            'Black',
            'Green',
            'Purple'
        ], $instance->toArray(), 'Expected original to have remained untouched');
    }

    public function testTryWithoutValuesThrowingExceptionBecauseOfNonExistingValue() : void
    {
        $this->expectException(ValueNotFound::class);
        $this->expectExceptionMessage(
            'Value "Red" was not found. Available values: "Orange"'
        );

        $instance = StringCollectionType::fromArray(['Orange', 'Green']);
        $instance->tryWithoutValues(['Green', 'Red']);
    }

    public function testTryWithoutValuesBeingSuccessful() : void
    {
        $instance  = StringCollectionType::fromArray(['Orange', 'Black', 'Green', 'Purple']);
        $instance2 = $instance->tryWithoutValues(['Black', 'Purple']);

        $this->assertEquals([
            'Orange',
            'Green',
        ], $instance2->toArray(), 'Expected new array to match');
        $this->assertEquals([
            'Orange',
            'Black',
            'Green',
            'Purple',
        ], $instance->toArray(), 'Expected original to have remained untouched');
    }

    public function testTryWithoutValuesWithEmptyArray() : void
    {
        $instance  = StringCollectionType::fromArray(['Orange', 'Black', 'Green', 'Purple']);
        $instance2 = $instance->tryWithoutValues([]);

        $this->assertEquals([
            'Orange',
            'Black',
            'Green',
            'Purple',
        ], $instance2->toArray(), 'Expected new array to match');
        $this->assertEquals([
            'Orange',
            'Black',
            'Green',
            'Purple',
        ], $instance->toArray(), 'Expected original to have remained untouched');
    }

    public function testTryWithoutValuesThrowingExceptionBecauseOfNowNonExistingValue() : void
    {
        $this->expectException(ValueNotFound::class);
        $this->expectExceptionMessage(
            'Value "Black" was not found. Available values: "Orange", "Green"'
        );

        $instance = StringCollectionType::fromArray(['Orange', 'Green']);
        $instance->tryWithoutValues(['Black', 'Orange', 'Black']);
    }
}