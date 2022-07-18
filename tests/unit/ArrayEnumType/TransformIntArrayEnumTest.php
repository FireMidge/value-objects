<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\ArrayEnumType;

use FireMidge\Tests\ValueObject\Unit\Classes\TransformIntArrayEnumType;
use FireMidge\ValueObject\Exception\InvalidValue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\TransformIntArrayEnumType
 */
class TransformIntArrayEnumTest extends TestCase
{
    public function validValueProvider() : array
    {
        return [
            [ [], [] ],
            [ [ 22 ], [ 22 ] ],
            [ [ 11 ], [ 11 ] ],
            [ [ 33 ], [ 33 ] ],
            [ [ 22, 11, 33 ], [ 22, 11, 33 ] ],
            [ [ '22' ], [ 22 ] ],
            [ [ (float) 11 ], [ 11 ] ],
            [ [ 33.2 ], [ 33 ] ],
            [ [ 33.2, 11.01, '22' ], [ 33, 11, 22 ] ],
        ];
    }

    /**
     * @dataProvider validValueProvider
     */
    public function testFromArrayWithValidValue(array $input, array $output) : void
    {
        $instance = TransformIntArrayEnumType::fromArray($input);
        $this->assertSame($output, $instance->toArray());
    }

    public function invalidValueProvider() : array
    {
        return [
            'floatRoundedDown' => [
                [ 10.9 ],
                'The following values are not valid: "10". Valid values are: "11", "22", "33"'
            ],
            'invalidString' => [
                [ '35' ],
                'The following values are not valid: "35". Valid values are: "11", "22", "33"'
            ],
            'invalidInt' => [
                [ 44 ],
                'The following values are not valid: "44". Valid values are: "11", "22", "33"'
            ],
            'mixedValidAndInvalid' => [
                [ 33, 44, 11, '55' ],
                'The following values are not valid: "44", "55". Valid values are: "11", "22", "33"'
            ],
            'mixedInvalidAndInvalidType' => [
                [ 33, 11, [ 44 ] ],
                'Invalid value. Must be of type "integer" but got "array"'
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
        TransformIntArrayEnumType::fromArray($values);
    }
}