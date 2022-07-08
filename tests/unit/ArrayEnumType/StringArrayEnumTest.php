<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\ArrayEnumType;

use FireMidge\Tests\ValueObject\Unit\Classes\SimpleObject;
use FireMidge\Tests\ValueObject\Unit\Classes\StringArrayEnumType;
use FireMidge\ValueObject\Exception\InvalidValue;
use FireMidge\ValueObject\Exception\ValueNotFound;
use PHPUnit\Framework\TestCase;

class StringArrayEnumTest extends TestCase
{
    public function validValueProvider() : array
    {
        return [
            [ [] ],
            [ [ 'name' ] ],
            [ [ 'email' ] ],
            [ [ 'status' ] ],
            [ [ 'name', 'email', 'status' ] ],
            [ [ 'name', 'email', 'status', 'status' ] ],
            [ [ 'email', 'status' ] ],
        ];
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringArrayEnumType::fromArray
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringArrayEnumType::toArray
     */
    public function testFromArrayWithValidValue(array $values) : void
    {
        $instance = StringArrayEnumType::fromArray($values);
        $this->assertSame($values, $instance->toArray());
    }

    public function invalidValueProvider() : array
    {
        return [
            'asObject' => [
                [ new SimpleObject('status') ],
                'Invalid value. Must be of type "string" but got "object"'
            ],
            'asInteger' => [
                [ 1 ],
                'Invalid value. Must be of type "string" but got "integer"'
            ],
            'invalidString' => [
                [ 'invalid' ],
                'The following values are not valid: "invalid". Valid values are: "name", "email", "status"'
            ],
            'mixedValidAndInvalid' => [
                [ 'status', 'name', 'invalid', 'email', 'other' ],
                'The following values are not valid: "invalid", "other". Valid values are: "name", "email", "status"'
            ],
        ];
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringArrayEnumType::fromArray
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringArrayEnumType::toArray
     */
    public function testFromArrayWithInvalidValue(array $values, string $expectedExceptionMessage) : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        StringArrayEnumType::fromArray($values);
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringArrayEnumType::withAll
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringArrayEnumType::toArray
     */
    public function testWithAll() : void
    {
        $instance = StringArrayEnumType::withAll();
        $this->assertEquals([
            'name',
            'email',
            'status',
        ], $instance->toArray());
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringArrayEnumType::fromArray
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringArrayEnumType::toArray
     */
    public function testFromArrayWithEmptyArray() : void
    {
        $instance = StringArrayEnumType::fromArray([]);
        $this->assertSame([], $instance->toArray());
    }

    public function singleValidValueProvider() : array
    {
        return [
            'name' => [ 'name' ],
            'email' => [ 'email' ],
            'status' => [ 'status' ],
        ];
    }

    /**
     * @dataProvider singleValidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringArrayEnumType::withValue
     *
     * @depends testFromArrayWithEmptyArray
     */
    public function testWithValueWithValidValue(string $value) : void
    {
        $instance    = StringArrayEnumType::fromArray([
            'email',
            'status',
        ]);
        $newInstance = $instance->withValue($value);

        $this->assertEquals([
            'email',
            'status',
            $value
        ], $newInstance->toArray(), 'Expected new instance to match');
        $this->assertEquals([
            'email',
            'status',
        ], $instance->toArray(), 'Expected old instance to have remained unchanged'); // Make sure the previous instance hasn't been changed
    }

    public function singleInvalidValueProvider() : array
    {
        return [
            'nam'     => ['nam', 'The following values are not valid: "nam". Valid values are: "name", "email", "status"'],
            'e'       => ['e', 'The following values are not valid: "e". Valid values are: "name", "email", "status"'],
            'invalid' => ['invalid', 'The following values are not valid: "invalid". Valid values are: "name", "email", "status"'],
            'empty'   => ['', 'The following values are not valid: "". Valid values are: "name", "email", "status"'],
            'int'     => [1, 'Invalid value. Must be of type "string" but got "integer"'],
            'bool'    => [false, 'Invalid value. Must be of type "string" but got "boolean"'],
            'object'  => [new SimpleObject('name'), 'Invalid value. Must be of type "string" but got "object"'],
        ];
    }

    /**
     * @dataProvider singleInvalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringArrayEnumType::withValue
     *
     * @depends testFromArrayWithEmptyArray
     */
    public function testWithValueWithInvalidValue($invalidValue, string $expectedExceptionMessage) : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $instance = StringArrayEnumType::fromArray([]);
        $instance->withValue($invalidValue);
    }

    public function withoutInvalidValueProvider() : array
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
                'Value "something" was not found. Available values: "status"'
            ],
            'invalidFour' => [
                [ 'status', 'email', 'name' ],
                'something',
                ValueNotFound::class,
                'Value "something" was not found. Available values: "status", "email", "name"'
            ],
            'invalidFive' => [
                [ 'status', 'email', 'status', 'name' ],
                'something',
                ValueNotFound::class,
                'Value "something" was not found. Available values: "status", "email", "status", "name"'
            ],
        ];
    }

    /**
     * @dataProvider withoutInvalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringArrayEnumType::tryWithoutValue
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

        $instance = StringArrayEnumType::fromArray($stateBefore);
        $instance->tryWithoutValue($valueToBeRemoved);
    }

    public function withoutValidValueProvider() : array
    {
        return [
            'one' => [
                [ 'name', 'email', 'status' ],
                'name',
                [ 'email', 'status' ],
            ],
            'two' => [
                [ 'name', 'name', 'email', 'status' ],
                'name',
                [ 'name', 'email', 'status' ],
            ],
            'three' => [
                [ 'name', 'email', 'status', 'name' ],
                'name',
                [ 'email', 'status', 'name' ],
            ],
            'four' => [
                [ 'status' ],
                'status',
                [ ],
            ],
            'five' => [
                [ 'email', 'status' ],
                'email',
                [ 'status' ],
            ],
        ];
    }

    /**
     * @dataProvider withoutValidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringArrayEnumType::tryWithoutValue
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringArrayEnumType::toArray
     */
    public function testTryWithoutValueDoesNotChangePreExisting(
        array $stateBefore,
        string $valueToBeRemoved,
        array $stateAfter
    ) : void
    {
        $instance = StringArrayEnumType::fromArray($stateBefore);
        $newInstance = $instance->tryWithoutValue($valueToBeRemoved);

        $this->assertEquals($stateAfter, $newInstance->toArray(), 'Expected new instance to match');
        $this->assertEquals($stateBefore, $instance->toArray(), 'Expected old instance to have remained unchanged');
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringArrayEnumType::withoutValue
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringArrayEnumType::toArray
     */
    public function testWithoutValueNotThrowingExceptionIfValueDidNotExist() : void
    {
        $instance = StringArrayEnumType::fromArray(['name']);
        $newInstance = $instance->withoutValue('email');

        $this->assertEquals(['name'], $newInstance->toArray());
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringArrayEnumType::withoutValue
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringArrayEnumType::toArray
     */
    public function testWithoutValueThrowingExceptionIfValueInvalid() : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('Invalid value. Must be of type "string" but got "object"');

        $instance = StringArrayEnumType::fromArray(['name']);
        $instance->withoutValue(new SimpleObject('name'));
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringArrayEnumType::contains
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringArrayEnumType::fromArray
     */
    public function testContains() : void
    {
        $instance = StringArrayEnumType::fromArray([
            'email',
            'name',
        ]);

        $this->assertTrue($instance->contains('email'), 'Expected to contain email');
        $this->assertTrue($instance->contains('name'), 'Expected to contain name');
        $this->assertFalse($instance->contains('status'), 'Expected not to contain status');
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringArrayEnumType::contains
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringArrayEnumType::fromArray
     */
    public function testContainsThrowingError() : void
    {
        $instance = StringArrayEnumType::fromArray([
            'status',
        ]);

        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage(
            'Invalid value. Must be of type "string" but got "boolean"'
        );

        $instance->contains(true);
    }
}