<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Helper;

use FireMidge\Tests\ValueObject\Unit\Classes\BasicStringCollectionType;
use FireMidge\Tests\ValueObject\Unit\Classes\BoolType;
use FireMidge\Tests\ValueObject\Unit\Classes\CapitalStringType;
use FireMidge\Tests\ValueObject\Unit\Classes\DoubleObject;
use FireMidge\Tests\ValueObject\Unit\Classes\DynamicClassArrayEnumType;
use FireMidge\Tests\ValueObject\Unit\Classes\IntEnumType;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleFloatType;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleIntType;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleObject;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleStringType;
use FireMidge\Tests\ValueObject\Unit\Classes\StringClassArrayEnumType;
use FireMidge\ValueObject\Exception\ConversionError;
use PHPUnit\Framework\TestCase;

/**
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\StringClassArrayEnumType
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\DynamicClassArrayEnumType
 */
class CanConvertToInstanceTest extends TestCase
{
    public function testConvertIntoInstance1() : void
    {
        $this->assertEquals(
            SimpleIntType::fromInt(200),
            StringClassArrayEnumType::convertIntoInstance(200, SimpleIntType::class)
        );
    }

    public function testConvertIntoInstance2() : void
    {
        $this->assertEquals(
            SimpleStringType::fromString('Hello world'),
            StringClassArrayEnumType::convertIntoInstance('Hello world', SimpleStringType::class)
        );
    }

    public function testConvertIntoInstance3() : void
    {
        $this->assertEquals(
            BasicStringCollectionType::fromArray(['Alpha', 'Beta', 'Gamma', 'Delta']),
            StringClassArrayEnumType::convertIntoInstance(
                ['Alpha', 'Beta', 'Gamma', 'Delta'],
                BasicStringCollectionType::class
            )
        );
    }

    public function testConvertIntoInstance4() : void
    {
        $this->assertEquals(
            BoolType::fromBool(true),
            StringClassArrayEnumType::convertIntoInstance(true, BoolType::class)
        );
    }

    public function testConvertIntoInstance5() : void
    {
        $this->assertEquals(
            SimpleFloatType::fromFloat(765.43),
            StringClassArrayEnumType::convertIntoInstance(765.43, SimpleFloatType::class)
        );
    }

    public function testConvertIntoInstance6() : void
    {
        $this->assertEquals(
            SimpleFloatType::fromFloat(765.0),
            StringClassArrayEnumType::convertIntoInstance(765, SimpleFloatType::class)
        );
    }

    public function testConvertIntoInstance7() : void
    {
        $this->assertEquals(
            DoubleObject::fromDouble(765.44),
            StringClassArrayEnumType::convertIntoInstance(765.44, DoubleObject::class)
        );
    }

    public function testFailedConversion() : void
    {
        $this->expectException(ConversionError::class);
        $this->expectExceptionMessage(sprintf(
            'Could not convert value "765.44" to %s. Override FireMidge\ValueObject\Helper\CanCreateInstance::convertIntoInstance to customise conversion',
            DoubleObject::class
        ));

        StringClassArrayEnumType::convertIntoInstance('765.44', DoubleObject::class);
    }

    public function testAllowToStringConversionDefaultsToFalse() : void
    {
        $this->assertFalse(StringClassArrayEnumType::shouldAllowToStringConversion());
    }

    public function testAllowStringConversionEndsInSuccess() : void
    {
        DynamicClassArrayEnumType::useClass(IntEnumType::class);
        DynamicClassArrayEnumType::allowToStringConversion(true);

        $this->assertEquals(
            IntEnumType::fromInt(4),
            DynamicClassArrayEnumType::convertIntoInstance('4', IntEnumType::class)
        );
    }

    public function invalidValueForStringConversionProvider() : array
    {
        return [
            [false, 'false'],
            [[1, 2], 'Array(1, 2)'],
            [new SimpleObject('Hi'), '"Hi"'],
        ];
    }

    /**
     * @dataProvider invalidValueForStringConversionProvider
     */
    public function testAllowStringConversionWithInvalidValue(mixed $value, string $expectedTypeInError) : void
    {
        DynamicClassArrayEnumType::useClass(IntEnumType::class);
        DynamicClassArrayEnumType::allowToStringConversion(true);

        $this->expectException(ConversionError::class);
        $this->expectExceptionMessage(sprintf(
            'Could not convert value %s to %s. Override FireMidge\ValueObject\Helper\CanCreateInstance::convertIntoInstance to customise conversion',
            $expectedTypeInError,
            IntEnumType::class
        ));

        DynamicClassArrayEnumType::convertIntoInstance($value, IntEnumType::class);
        DynamicClassArrayEnumType::allowToStringConversion(false); // Reset
    }

    public function testAllowStringConversionWithValidValue() : void
    {
        DynamicClassArrayEnumType::useClass(IntEnumType::class);
        DynamicClassArrayEnumType::allowToStringConversion(true);

        $this->assertEquals(
            SimpleStringType::fromString('700'),
            DynamicClassArrayEnumType::convertIntoInstance(700, SimpleStringType::class)
        );

        DynamicClassArrayEnumType::allowToStringConversion(false); // Reset
    }

    public function testAllowStringConversionWithInvalidValuePassedToFromString() : void
    {
        DynamicClassArrayEnumType::useClass(CapitalStringType::class);
        DynamicClassArrayEnumType::allowToStringConversion(true);

        $this->expectException(ConversionError::class);
        $this->expectExceptionMessage(sprintf(
            'Could not convert value 8000 to %s. Override FireMidge\ValueObject\Helper\CanCreateInstance::convertIntoInstance to customise conversion',
            CapitalStringType::class
        ));

        DynamicClassArrayEnumType::convertIntoInstance(8000, CapitalStringType::class);
        DynamicClassArrayEnumType::allowToStringConversion(false); // Reset
    }
}