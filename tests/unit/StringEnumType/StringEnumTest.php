<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\StringEnumType;

use FireMidge\Tests\ValueObject\Unit\Classes\SimpleNonConvertableObject;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleObject;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleStringType;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleTextObject;
use FireMidge\Tests\ValueObject\Unit\Classes\StringEnumType;
use FireMidge\ValueObject\Exception\ConversionError;
use FireMidge\ValueObject\Exception\InvalidValue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringEnumType
 */
class StringEnumTest extends TestCase
{
    public function validValueProvider() : array
    {
        return [
            [StringEnumType::SPRING ],
            [StringEnumType::SUMMER ],
            [StringEnumType::AUTUMN ],
            [StringEnumType::WINTER ],
        ];
    }

    /**
     * @dataProvider validValueProvider
     */
    public function testFromStringWithValidValue(string $value) : void
    {
        $instance = StringEnumType::fromString($value);
        $this->assertSame($value, $instance->toString());
    }

    /**
     * @dataProvider validValueProvider
     */
    public function testFromStringOrNullWithValidValue(string $value) : void
    {
        $instance = StringEnumType::fromStringOrNull($value);
        $this->assertSame($value, $instance->toString());
    }

    /**
     * @dataProvider validValueProvider
     */
    public function testMagicToString(string $value) : void
    {
        $instance = StringEnumType::fromString($value);
        $this->assertEquals($value, $instance);
    }

    public function invalidValueProvider() : array
    {
        return [
            [ '0' ],
            [ '1' ],
            [ 'invalid' ],
            [ 'summer1' ],
            [ '1summer' ],
            [ ' summer' ],
            [ 'SUMMER' ],
            [ 'summer ' ],
        ];
    }

    public function testFromStringOrNullWithNull() : void
    {
        $instance = StringEnumType::fromStringOrNull(null);
        $this->assertNull($instance);
    }

    /**
     * @dataProvider invalidValueProvider
     */
    public function testFromStringWithInvalidValue(string $value) : void
    {
        $this->expectException(InvalidValue::class);
        StringEnumType::fromString($value);
    }

    /**
     * @dataProvider invalidValueProvider
     */
    public function testFromStringWithInvalidValueErrorMessage(string $value) : void
    {
        $this->expectExceptionMessage(sprintf(
            'Value "%s" is invalid. Must be one of: "spring", "summer", "autumn", "winter"',
            $value
        ));
        StringEnumType::fromString($value);
    }

    /**
     * @dataProvider invalidValueProvider
     */
    public function testFromStringOrNullWithInvalidValue(string $value) : void
    {
        $this->expectException(InvalidValue::class);
        StringEnumType::fromStringOrNull($value);
    }

    /**
     * @dataProvider invalidValueProvider
     */
    public function testFromStringOrNullWithInvalidValueErrorMessage(string $value) : void
    {
        $this->expectExceptionMessage(sprintf(
            'Value "%s" is invalid. Must be one of: "spring", "summer", "autumn", "winter"',
            $value
        ));
        StringEnumType::fromStringOrNull($value);
    }


    public function testIsEqualWithSameTypeSuccessful() : void
    {
        $instance1 = StringEnumType::autumn();
        $instance2 = StringEnumType::autumn();

        $this->assertTrue($instance1->isEqualTo($instance2, false));
        $this->assertFalse($instance1->isNotEqualTo($instance2, false));

        $this->assertTrue($instance1->isEqualTo($instance2), 'isEqualTo with strict check');
        $this->assertFalse($instance2->isNotEqualTo($instance1), 'isNotEqualTo with strict check');
    }

    public function successfulLooseCheckComparisonsProvider() : array
    {
        return [
            [ SimpleStringType::fromString('spring') ],
            [ 'spring' ],
            [ new SimpleObject('spring') ],
            [ new SimpleTextObject('spring') ],
        ];
    }

    /**
     * @dataProvider successfulLooseCheckComparisonsProvider
     */
    public function testEqualsToOnlyWithLooseCheckSuccessful(mixed $other) : void
    {
        $instance1 = StringEnumType::spring();
        $instance2 = $other;

        $this->assertTrue($instance1->isEqualTo($instance2, false));
        $this->assertFalse($instance1->isNotEqualTo($instance2, false));

        $this->assertFalse($instance1->isEqualTo($instance2), 'isEqualTo with strict check');
        $this->assertTrue($instance1->isNotEqualTo($instance2), 'isNotEqualTo with strict check');
    }

    public function unsuccessfulLooseCheckComparisonsProvider() : array
    {
        return [
            [ 'Spring' ],
            [ ' spring' ],
            [ 'spring ' ],
            [ '1' ],
            [ 'true' ],
            [ null ],
            [ new SimpleObject('Spring') ],
            [ new SimpleTextObject('spring ') ],
        ];
    }

    /**
     * @dataProvider unsuccessfulLooseCheckComparisonsProvider
     */
    public function testIsEqualEvenWithLooseCheckUnsuccessful(mixed $other) : void
    {
        $instance1 = StringEnumType::spring();
        $instance2 = $other;

        $this->assertFalse($instance1->isEqualTo($instance2, false));
        $this->assertTrue($instance1->isNotEqualTo($instance2, false));

        $this->assertFalse($instance1->isEqualTo($instance2), 'isEqualTo with strict check');
        $this->assertTrue($instance1->isNotEqualTo($instance2), 'isNotEqualTo with strict check');
    }

    public function testConversionErrorTriggered() : void
    {
        $instance1 = StringEnumType::spring();
        $instance2 = new SimpleNonConvertableObject('spring');

        $this->expectException(ConversionError::class);
        $this->expectExceptionMessage(sprintf(
            'Could not convert value of type %s to string. Make sure the class has one of these methods: ',
            SimpleNonConvertableObject::class
        ));

        $instance1->isEqualTo($instance2, false);
    }
}