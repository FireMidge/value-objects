<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\ArrayEnumType;

use FireMidge\Tests\ValueObject\Unit\Classes\TransformIntArrayEnumType;
use FireMidge\ValueObject\Exception\InvalidValue;
use PHPUnit\Framework\TestCase;

/**
 * Note: We are saying here that it also covers ObjectArrayEnumType, which isn't technically true.
 * It's a bit of a hack in order to get a more accurate mutation test coverage.
 *
 * This test class right here tests (apart from others) methods that are inside IsArrayEnumType.
 * TransformIntArrayEnumType is using the trait, but via a parent instead of directly.
 * Infection does not recognise this and claims making certain changes to IsArrayEnumType causes
 * escaped mutants, when in fact, they are covered here and lead to failing unit tests.
 *
 * In other classes, we've simply re-included the parent's traits in order for Infection to recognise
 * them as covered, but in the case of IsIntArrayEnumType (which TransformIntArrayEnumType is using),
 * it would lead to a clash due to an overridden method.
 *
 * So, we're saying that ObjectArrayEnumType, which directly includes IsArrayEnumType, is covered here,
 * but only to "trick" Infection, as it won't recognise it in the conventional way.
 *
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\TransformIntArrayEnumType
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ObjectArrayEnumType
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