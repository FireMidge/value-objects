<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\ArrayEnumType;

use FireMidge\Tests\ValueObject\Unit\Classes\StringEnumType;
use FireMidge\Tests\ValueObject\Unit\Classes\StringVOArrayEnumType;
use FireMidge\ValueObject\Exception\DuplicateValue;
use FireMidge\ValueObject\Exception\InvalidValue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringVOArrayEnumType
 * @uses \FireMidge\Tests\ValueObject\Unit\Classes\StringEnumType
 */
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
     */
    public function testFromArrayWithInvalidValue(array $values, string $expectedExceptionMessage) : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        StringVOArrayEnumType::fromArray($values);
    }

    public function testValuesAreUnique() : void
    {
        $this->expectException(DuplicateValue::class);
        $this->expectExceptionMessage(
            'Values contain duplicates. Only unique values allowed. Values passed: "autumn", "summer", "summer"'
        );
        StringVOArrayEnumType::fromArray([
            StringEnumType::autumn(),
            StringEnumType::summer(),
            StringEnumType::summer(),
        ]);
    }

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

    public function testIsEqualWithSameTypeSuccessful() : void
    {
        $instance1 = StringVOArrayEnumType::fromArray([
            StringEnumType::winter(),
            StringEnumType::autumn(),
        ]);

        $instance2 = StringVOArrayEnumType::fromArray([
            StringEnumType::winter(),
            StringEnumType::autumn(),
        ]);

        $this->assertTrue($instance1->isEqualTo($instance2));
        $this->assertFalse($instance1->isNotEqualTo($instance2));

        $this->assertTrue($instance1->isEqualTo($instance2, false), 'isEqualTo without strict check');
        $this->assertFalse($instance1->isNotEqualTo($instance2, false), 'isNotEqualTo without strict check');
    }

    public function testIsEqualWithArraySuccessful() : void
    {
        $instance = StringVOArrayEnumType::fromArray([
            StringEnumType::winter(),
            StringEnumType::autumn(),
        ]);

        $array = ['autumn', 'winter'];

        $this->assertTrue(
            $instance->isEqualTo($array, false),
            'Expected comparison to succeed without strict check'
        );
        $this->assertFalse(
            $instance->isNotEqualTo($array, false),
            'Expected isNotEqualTo comparison to fail without strict check'
        );

        $this->assertFalse($instance->isEqualTo($array), 'Expected comparison to fail with strict check');
        $this->assertTrue($instance->isNotEqualTo($array), 'Expected isNotEqualTo comparison to succeed with strict check');
    }

    public function testEqualWithNullReturnsFalse() : void
    {
        $instance = StringVOArrayEnumType::fromArray([
            StringEnumType::winter(),
            StringEnumType::autumn(),
        ]);

        $this->assertFalse($instance->isEqualTo(null, false));
        $this->assertTrue($instance->isNotEqualTo(null, false));

        $this->assertFalse($instance->isEqualTo(null, true));
        $this->assertTrue($instance->isNotEqualTo(null, true));
    }

    public function testCount() : void
    {
        $instance = StringVOArrayEnumType::fromArray(
            [
                StringEnumType::winter(),
                StringEnumType::autumn(),
            ],
        );

        $this->assertSame(2, $instance->count());
    }
}