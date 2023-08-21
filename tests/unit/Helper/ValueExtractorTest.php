<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Helper;

use FireMidge\Tests\ValueObject\Unit\Classes\DoubleObject;
use FireMidge\Tests\ValueObject\Unit\Classes\NumberObject;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleFloatType;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleIntType;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleNonConvertableObject;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleObject;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleStringType;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleTextObject;
use FireMidge\Tests\ValueObject\Unit\Classes\StringEnumType;
use FireMidge\Tests\ValueObject\Unit\Classes\ValueExtractor;
use FireMidge\ValueObject\Exception\ConversionError;
use PHPUnit\Framework\TestCase;

/**
 * Tests CanExtractValueOfType trait.
 *
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ValueExtractor
 */
class ValueExtractorTest extends TestCase
{
    public function stringValueProvider() : array
    {
        return [
            'isStringValue' => ['Aquí hay algun texto', 'Aquí hay algun texto'],
            'toString'      => [
                SimpleStringType::fromString('Grüne Särge hört man nicht'),
                'Grüne Särge hört man nicht',
            ],
            '__toString'    => [new SimpleObject('Hester har hover'), 'Hester har hover'],
            'toText'        => [new SimpleTextObject('øæÆsåäüpyrö'), 'øæÆsåäüpyrö'],
        ];
    }

    /**
     * @dataProvider stringValueProvider
     */
    public function testGetStringValueSuccessful(mixed $input, string $extractedText) : void
    {
        $this->assertSame($extractedText, (new ValueExtractor())->getStringValueOfOther($input));
    }

    public function testGetStringValueConversionErrorTriggered() : void
    {
        $instance = new SimpleNonConvertableObject('spring');

        $this->expectException(ConversionError::class);
        $this->expectExceptionMessage(sprintf(
            'Could not convert value of type %s to string. Make sure the class has one of these methods: "toString", "toText", "__toString"',
            SimpleNonConvertableObject::class
        ));

        (new ValueExtractor())->getStringValueOfOther($instance);
    }

    public function intValueProvider() : array
    {
        return [
            'isIntValue'    => [15, 15],
            'isFloatValue1' => [16.8, 16],
            'isFloatValue2' => [18.0, 18],
            'toInt'         => [SimpleIntType::fromInt(20), 20],
            'toFloat'       => [SimpleFloatType::fromFloat(23.6), 23],
            'toDouble'      => [new DoubleObject(25.3), 25],
            'toNumber'      => [new NumberObject(30), 30],
        ];
    }

    /**
     * @dataProvider intValueProvider
     */
    public function testGetIntValueSuccessful(mixed $input, int $extractedInt) : void
    {
        $this->assertSame($extractedInt, (new ValueExtractor())->getIntValueOfOther($input));
    }

    public function testGetIntValueConversionErrorTriggered() : void
    {
        $instance = new SimpleNonConvertableObject('15');

        $this->expectException(ConversionError::class);
        $this->expectExceptionMessage(sprintf(
            'Could not convert value of type %s to int. Make sure the class has one of these methods: "toInt", "toFloat", "toDouble", "toNumber"',
            SimpleNonConvertableObject::class
        ));

        (new ValueExtractor())->getIntValueOfOther($instance);
    }

    public function floatValueProvider() : array
    {
        return [
            'isFloatValue1' => [16.8, 16.8],
            'isFloatValue2' => [18.0, 18.0],
            'isIntValue'    => [15, 15.0],
            'toFloat'       => [SimpleFloatType::fromFloat(23.6), 23.6],
            'toDouble'      => [new DoubleObject(25.3), 25.3],
            'toInt'         => [SimpleIntType::fromInt(20), 20.0],
            'toNumber1'     => [new NumberObject(30), 30.0],
            'toNumber2'     => [new NumberObject(30.9111), 30.9111],
        ];
    }

    /**
     * @dataProvider floatValueProvider
     */
    public function testGetFloatValueSuccessful(mixed $input, float $extractedInt) : void
    {
        $this->assertSame($extractedInt, (new ValueExtractor())->getFloatValueOfOther($input));
    }

    public function testGetFloatValueConversionErrorTriggered() : void
    {
        $instance = new SimpleNonConvertableObject('15.5');

        $this->expectException(ConversionError::class);
        $this->expectExceptionMessage(sprintf(
            'Could not convert value of type %s to float. Make sure the class has one of these methods: "toFloat", "toDouble", "toInt", "toNumber"',
            SimpleNonConvertableObject::class
        ));

        (new ValueExtractor())->getFloatValueOfOther($instance);
    }
}