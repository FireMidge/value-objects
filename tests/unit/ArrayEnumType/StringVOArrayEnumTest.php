<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\ArrayEnumType;

use FireMidge\Tests\ValueObject\Unit\Classes\StringEnumType;
use FireMidge\Tests\ValueObject\Unit\Classes\StringVOArrayEnumType;
use FireMidge\ValueObject\Exception\InvalidValue;
use PHPUnit\Framework\TestCase;

class StringVOArrayEnumTest extends TestCase
{
    public function validValueProvider() : array
    {
        return [
            [ [] ],
            [ [ StringEnumType::spring()] ],
            [ [ StringEnumType::summer()] ],
            [ [ StringEnumType::autumn()] ],
            [ [ StringEnumType::winter()] ],
            [ [ StringEnumType::spring(), StringEnumType::winter()] ],
            [ [ StringEnumType::spring(), StringEnumType::summer(), StringEnumType::autumn(), StringEnumType::winter()] ],
        ];
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringVOArrayEnumType::fromArray
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringVOArrayEnumType::toArray
     */
    public function testFromArrayWithValidValue(array $values) : void
    {
        $instance = StringVOArrayEnumType::fromArray($values);
        $this->assertSame($values, $instance->toArray());
    }

    public function invalidValueProvider() : array
    {
        return [
            'springAsString' => [
                [ 'spring' ],
                'Invalid value. Must be an object and an instance of "FireMidge\Tests\ValueObject\Unit\Classes\StringEnumType"'
            ],
        ];
    }

    /**
     * @dataProvider invalidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringVOArrayEnumType::fromArray
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringVOArrayEnumType::toArray
     */
    public function testFromArrayWithInvalidValue(array $values, string $expectedExceptionMessage) : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        StringVOArrayEnumType::fromArray($values);
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringVOArrayEnumType::fromArray
     */
    public function testValuesAreUnique() : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage(
            'Values contain duplicates. Only unique values allowed. Values passed: "autumn", "summer", "summer"'
        );
        StringVOArrayEnumType::fromArray([
            StringEnumType::autumn(),
            StringEnumType::summer(),
            StringEnumType::summer(),
        ]);
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringVOArrayEnumType::withAll
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringVOArrayEnumType::toArray
     */
    public function testWithAll() : void
    {
        $instance = StringVOArrayEnumType::withAll();
        $this->assertEquals([
            StringEnumType::spring(),
            StringEnumType::summer(),
            StringEnumType::autumn(),
            StringEnumType::winter(),
        ], $instance->toArray());
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringVOArrayEnumType::fromArray
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringVOArrayEnumType::toArray
     */
    public function testFromArrayWithEmptyArray() : void
    {
        $instance = StringVOArrayEnumType::fromArray([]);
        $this->assertSame([], $instance->toArray());
    }

    public function singleValidValueProvider() : array
    {
        return [
            'summer' => [ StringEnumType::summer(), ],
            'autumn' => [ StringEnumType::autumn(), ],
            'winter' => [ StringEnumType::winter(), ],
        ];
    }

    /**
     * @dataProvider singleValidValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringVOArrayEnumType::withValue
     *
     * @depends testFromArrayWithEmptyArray
     */
    public function testWithValueWithValidValue(StringEnumType $value) : void
    {
        $instance    = StringVOArrayEnumType::fromArray([
            StringEnumType::spring(),
        ]);
        $newInstance = $instance->withValue($value);

        $this->assertEquals([
            StringEnumType::spring(),
            $value
        ], $newInstance->toArray(), 'Expected new instance to match');
        $this->assertEquals([
            StringEnumType::spring()
        ], $instance->toArray(), 'Expected old instance to have remained unchanged'); // Make sure the previous instance hasn't been changed
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringVOArrayEnumType::withValue
     */
    public function testWithValueThrowsOnDuplicate() : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage(
            'Value "spring" cannot be used as it already exists within array. Existing values: "spring", "winter"'
        );

        $instance = StringVOArrayEnumType::fromArray([
            StringEnumType::spring(),
            StringEnumType::winter(),
        ]);
        $instance->withValue(StringEnumType::spring());
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringVOArrayEnumType::contains
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringVOArrayEnumType::fromArray
     */
    public function testContains() : void
    {
        $instance = StringVOArrayEnumType::fromArray([
            StringEnumType::winter(),
            StringEnumType::spring(),
        ]);

        $this->assertTrue($instance->contains(StringEnumType::winter()), 'Expected to contain winter');
        $this->assertTrue($instance->contains(StringEnumType::spring()), 'Expected to contain spring');
        $this->assertFalse($instance->contains(StringEnumType::summer()), 'Expected not to contain summer');
        $this->assertFalse($instance->contains(StringEnumType::autumn()), 'Expected not to contain autumn');
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringVOArrayEnumType::contains
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringVOArrayEnumType::fromArray
     */
    public function testContainsThrowingError() : void
    {
        $instance = StringVOArrayEnumType::fromArray([
            StringEnumType::winter(),
        ]);

        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage(
            'Invalid value. Must be an object and an instance of "FireMidge\Tests\ValueObject\Unit\Classes\StringEnumType"'
        );

        $instance->contains('autumn');
    }
}