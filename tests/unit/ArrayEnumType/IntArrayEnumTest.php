<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\ArrayEnumType;

use FireMidge\Tests\ValueObject\Unit\Classes\IntArrayEnumType;
use FireMidge\ValueObject\Exception\InvalidValue;
use FireMidge\ValueObject\Exception\ValueNotFound;
use PHPUnit\Framework\TestCase;

/**
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntArrayEnumType
 */
class IntArrayEnumTest extends TestCase
{
    public function validValueProvider() : array
    {
        return [
            [ [] ],
            [ [ 22 ] ],
            [ [ 11 ] ],
            [ [ 33 ] ],
            [ [ 22, 11, 33 ] ],
            [ [ 11, 22, 33 ] ],
            [ [ 33, 11 ] ],
        ];
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntArrayEnumType::fromArray
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntArrayEnumType::toArray
     */
    public function testFromArrayWithValidValue(array $values) : void
    {
        $instance = IntArrayEnumType::fromArray($values);
        $this->assertSame($values, $instance->toArray());
    }

    public function invalidValueProvider() : array
    {
        return [
            'asFloat' => [
                [ (float) 11 ],
                'Invalid value. Must be of type "integer" but got "double"' // Yep, it says double when passing float
            ],
            'asString' => [
                [ (string) 11 ],
                'Invalid value. Must be of type "integer" but got "string"'
            ],
            'invalidInt' => [
                [ 44 ],
                'The following values are not valid: "44". Valid values are: "11", "22", "33"'
            ],
            'mixedValidAndInvalid' => [
                [ 33, 44, 11, 55 ],
                'The following values are not valid: "44", "55". Valid values are: "11", "22", "33"'
            ],
            'mixedInvalidAndInvalidType' => [
                [ 33, 44, 11, 22.1 ],
                'Invalid value. Must be of type "integer" but got "double"'
            ],
        ];
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntArrayEnumType::fromArray
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntArrayEnumType::toArray
     */
    public function testFromArrayWithInvalidValue(array $values, string $expectedExceptionMessage) : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        IntArrayEnumType::fromArray($values);
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntArrayEnumType::withAll
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntArrayEnumType::toArray
     */
    public function testWithAll() : void
    {
        $instance = IntArrayEnumType::withAll();
        $this->assertEquals([
            11,
            22,
            33,
        ], $instance->toArray());
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntArrayEnumType::fromArray
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntArrayEnumType::toArray
     */
    public function testFromArrayWithEmptyArray() : void
    {
        $instance = IntArrayEnumType::fromArray([]);
        $this->assertSame([], $instance->toArray());
    }

    public function singleValidValueProvider() : array
    {
        return [
            '11' => [ 11 ],
            '22' => [ 22 ],
            '33' => [ 33 ],
        ];
    }

    /**
     * @dataProvider singleValidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntArrayEnumType::withValue
     *string
     * @depends testFromArrayWithEmptyArray
     */
    public function testWithValueWithValidValue(int $value) : void
    {
        $instance    = IntArrayEnumType::fromArray([
            11,
            33,
        ]);
        $newInstance = $instance->withValue($value);

        $this->assertEquals([
            11,
            33,
            $value
        ], $newInstance->toArray(), 'Expected new instance to match');
        $this->assertEquals([
            11,
            33,
        ], $instance->toArray(), 'Expected old instance to have remained unchanged'); // Make sure the previous instance hasn't been changed
    }

    public function singleInvalidValueProvider() : array
    {
        return  [
            '11.1'    => [ 11.1, 'Invalid value. Must be of type "integer" but got "double"' ],
            '11.0001' => [ 11.0001, 'Invalid value. Must be of type "integer" but got "double"' ],
            'float'   => [ (float) 11.001, 'Invalid value. Must be of type "integer" but got "double"' ], // Yep, it comes back as double even when passing float
            '44'      => [ 44, 'The following values are not valid: "44". Valid values are: "11", "22", "33"' ],
            '1'       => [ 1, 'The following values are not valid: "1". Valid values are: "11", "22", "33"' ],
            '0'       => [ 0, 'The following values are not valid: "0". Valid values are: "11", "22", "33"' ],
            '-11'     => [ -11, 'The following values are not valid: "-11". Valid values are: "11", "22", "33"' ],
            'empty'   => [ '', 'Invalid value. Must be of type "integer" but got "string"' ],
            'string'  => [ '11', 'Invalid value. Must be of type "integer" but got "string"' ],
            'bool'    => [ false, 'Invalid value. Must be of type "integer" but got "boolean"' ],
            'object'  => [ (object) 11, 'Invalid value. Must be of type "integer" but got "object"' ],
            'array'   => [ [11], 'Invalid value. Must be of type "integer" but got "array"' ],
        ];
    }

    /**
     * @dataProvider singleInvalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntArrayEnumType::withValue
     *
     * @depends testFromArrayWithEmptyArray
     */
    public function testWithValueWithInvalidValue($invalidValue, string $expectedExceptionMessage) : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $instance = IntArrayEnumType::fromArray([]);
        $instance->withValue($invalidValue);
    }

    public function withoutInvalidValueProvider() : array
    {
        return [
            'invalidOne' => [
                [ 22 ],
                new \stdClass(22),
                InvalidValue::class,
                'Invalid value. Must be of type "integer" but got "object"',
            ],
            'invalidTwo' => [
                [ 11 ],
                '11',
                InvalidValue::class,
                'Invalid value. Must be of type "integer" but got "string"',
            ],
            'invalidThree' => [
                [ 33 ],
                34,
                ValueNotFound::class,
                'Value "34" was not found. Available values: "33"'
            ],
            'invalidFour' => [
                [ 33, 22, 11 ],
                25,
                ValueNotFound::class,
                'Value "25" was not found. Available values: "33", "22", "11"'
            ],
            'invalidFive' => [
                [ 33, 22, 33, 11 ],
                0,
                ValueNotFound::class,
                'Value "0" was not found. Available values: "33", "22", "33", "11"'
            ],
        ];
    }

    /**
     * @dataProvider withoutInvalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntArrayEnumType::tryWithoutValue
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

        $instance = IntArrayEnumType::fromArray($stateBefore);
        $instance->tryWithoutValue($valueToBeRemoved);
    }

    public function withoutValidValueProvider() : array
    {
        return [
            'one' => [
                [ 11, 22, 33 ],
                11,
                [ 22, 33 ],
            ],
            'two' => [
                [ 11, 11, 22, 33 ],
                11,
                [ 11, 22, 33 ],
            ],
            'three' => [
                [ 11, 22, 33, 11 ],
                11,
                [ 22, 33, 11 ],
            ],
            'four' => [
                [ 33 ],
                33,
                [ ],
            ],
            'five' => [
                [ 22, 33 ],
                22,
                [ 33 ],
            ],
        ];
    }

    /**
     * @dataProvider withoutValidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntArrayEnumType::tryWithoutValue
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntArrayEnumType::toArray
     */
    public function testTryWithoutValueDoesNotChangePreExisting(
        array $stateBefore,
        int $valueToBeRemoved,
        array $stateAfter
    ) : void
    {
        $instance = IntArrayEnumType::fromArray($stateBefore);
        $newInstance = $instance->tryWithoutValue($valueToBeRemoved);

        $this->assertEquals($stateAfter, $newInstance->toArray(), 'Expected new instance to match');
        $this->assertEquals($stateBefore, $instance->toArray(), 'Expected old instance to have remained unchanged');
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntArrayEnumType::withoutValue
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntArrayEnumType::toArray
     */
    public function testWithoutValueNotThrowingExceptionIfValueDidNotExist() : void
    {
        $instance = IntArrayEnumType::fromArray([11]);
        $newInstance = $instance->withoutValue(22);

        $this->assertEquals([11], $newInstance->toArray());
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntArrayEnumType::withoutValue
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntArrayEnumType::toArray
     */
    public function testWithoutValueThrowingExceptionIfValueInvalid() : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('Invalid value. Must be of type "integer" but got "string"');

        $instance = IntArrayEnumType::fromArray([11]);
        $instance->withoutValue('11');
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntArrayEnumType::contains
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntArrayEnumType::fromArray
     */
    public function testContains() : void
    {
        $instance = IntArrayEnumType::fromArray([
            22,
            11,
        ]);

        $this->assertTrue($instance->contains(22), 'Expected to contain 22');
        $this->assertTrue($instance->contains(11), 'Expected to contain 11');
        $this->assertFalse($instance->contains(33), 'Expected not to contain 33');
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntArrayEnumType::contains
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntArrayEnumType::fromArray
     */
    public function testContainsThrowingError() : void
    {
        $instance = IntArrayEnumType::fromArray([
            33,
        ]);

        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage(
            'Invalid value. Must be of type "integer" but got "boolean"'
        );

        $instance->contains(true);
    }
}