<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\FloatType;

use FireMidge\Tests\ValueObject\Unit\Classes\DoubleObject;
use FireMidge\Tests\ValueObject\Unit\Classes\NegativeFloatType;
use FireMidge\Tests\ValueObject\Unit\Classes\NumberObject;
use FireMidge\Tests\ValueObject\Unit\Classes\OddIntType;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleFloatType;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleIntType;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleStringType;
use FireMidge\ValueObject\Exception\ConversionError;
use PHPUnit\Framework\TestCase;

/**
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\SimpleFloatType
 */
class OperationsTest extends TestCase
{
    public function successfulAddOperationsProvider() : array
    {
        return [
            [ 5, 20.5 ],
            [ 5.9, 21.4 ],
            [ SimpleIntType::fromInt(9), 24.5 ],
            [ OddIntType::fromInt(3), 18.5 ],
            [ SimpleFloatType::fromFloat(3.8), 19.3 ],
            [ new NumberObject(10.5), 26 ],
            [ new NumberObject(11), 26.5 ],
            [ new DoubleObject(17.01), 32.510000000000005 ], // Due to float rounding error
        ];
    }

    /**
     * @dataProvider successfulAddOperationsProvider
     */
    public function testAddSuccessful(mixed $thingToAdd, float $expectedResult) : void
    {
        $original = SimpleFloatType::fromFloat(15.5);
        $new      = $original->add($thingToAdd);

        $this->assertSame(15.5, $original->toFloat(), 'Did not expect the original to be updated.');
        $this->assertSame($expectedResult, $new->toFloat(), 'New instance does not match expected.');
    }

    public function testAddUnsuccessful() : void
    {
        $this->expectException(ConversionError::class);
        $this->expectExceptionMessage('Could not convert value "hello" to float. Make sure the class has one of these methods: "toFloat", "toDouble", "toInt", "toNumber"');

        SimpleFloatType::fromFloat(20.6)->add(SimpleStringType::fromString('hello'));
    }

    public function successfulSubtractOperationsProvider() : array
    {
        return [
            [ 5, 10.5 ],
            [ 5.9, 9.6 ],
            [ SimpleIntType::fromInt(9), 6.5 ],
            [ OddIntType::fromInt(3), 12.5 ],
            [ SimpleFloatType::fromFloat(3.8), 11.7 ],
            [ new NumberObject(10.5), 5 ],
            [ new NumberObject(11), 4.5 ],
            [ new DoubleObject(17.01), -1.5100000000000016 ], // due to float rounding error
        ];
    }

    /**
     * @dataProvider successfulSubtractOperationsProvider
     */
    public function testSubtractSuccessful(mixed $thingToSubtract, float $expectedResult) : void
    {
        $original = NegativeFloatType::fromFloat(15.5);
        $new      = $original->subtract($thingToSubtract);

        $this->assertSame(15.5, $original->toFloat(), 'Did not expect the original to be updated.');
        $this->assertSame($expectedResult, $new->toFloat(), 'New instance does not match expected.');
    }

    public function successfulGreaterThanComparisonProvider() : array
    {
        return [
            [ 5, true ],
            [ 5.9, true ],
            [ 11, false ],
            [ 11.9, false ],
            [ 10, true ],
            [ SimpleIntType::fromInt(9), true ],
            [ SimpleIntType::fromInt(11), false ],
            [ SimpleIntType::fromInt(10), true ],
            [ OddIntType::fromInt(9), true ],
            [ OddIntType::fromInt(11), false ],
            [ SimpleFloatType::fromFloat(9.9), true ],
            [ SimpleFloatType::fromFloat(11.1), false ],
            [ SimpleFloatType::fromFloat(10.0), true ],
            [ new NumberObject(9.9), true ],
            [ new NumberObject(11.0), false ],
            [ new NumberObject(9), true ],
            [ new NumberObject(11), false ],
            [ new NumberObject(10), true ],
            [ new NumberObject(10.04), true ],
            [ new NumberObject(10.06), false ],
            [ new DoubleObject(9.09), true ],
            [ new DoubleObject(11.0), false ],
            [ new DoubleObject(10.06), false ],
            [ new DoubleObject(10.04), true ],
        ];
    }

    /**
     * @dataProvider successfulGreaterThanComparisonProvider
     */
    public function testGreaterThanComparisonSuccessful(mixed $toCompare, bool $expectedResult) : void
    {
        $original = NegativeFloatType::fromFloat(10.05);
        $this->assertSame($expectedResult, $original->isGreaterThan($toCompare));
    }

    /**
     * @dataProvider successfulGreaterThanComparisonProvider
     */
    public function testLessThanComparisonSuccessful(mixed $toCompare, bool $expectedResult) : void
    {
        $original = NegativeFloatType::fromFloat(10.05);
        $this->assertSame(! $expectedResult, $original->isLessThan($toCompare));
    }

    public function equalDataProvider() : array
    {
        return [
            [ 10.05 ],
            [ SimpleFloatType::fromFloat(10.05) ],
            [ new NumberObject(10.05) ],
            [ new DoubleObject(10.05) ],
        ];
    }

    /**
     * @dataProvider equalDataProvider
     */
    public function testGreaterThanComparisonWithEqualValue(mixed $toCompare) : void
    {
        $original = NegativeFloatType::fromFloat(10.05);
        $this->assertSame(false, $original->isGreaterThan($toCompare));
    }

    /**
     * @dataProvider equalDataProvider
     */
    public function testLessThanComparisonWithEqualValue(mixed $toCompare) : void
    {
        $original = NegativeFloatType::fromFloat(10.05);
        $this->assertSame(false, $original->isLessThan($toCompare));
    }

    /**
     * @dataProvider equalDataProvider
     */
    public function testGreaterThanOrEqualToComparisonWithEqualValue(mixed $toCompare) : void
    {
        $original = NegativeFloatType::fromFloat(10.05);
        $this->assertSame(true, $original->isGreaterThanOrEqualTo($toCompare));
    }

    /**
     * @dataProvider equalDataProvider
     */
    public function testLessThanOrEqualToComparisonWithEqualValue(mixed $toCompare) : void
    {
        $original = NegativeFloatType::fromFloat(10.05);
        $this->assertSame(true, $original->isLessThanOrEqualTo($toCompare));
    }

    /**
     * @dataProvider equalDataProvider
     */
    public function testIsEqualToWithStrictCheckUnsuccessful(mixed $toCompare) : void
    {
        $original = NegativeFloatType::fromFloat(10.05);
        $this->assertSame(false, $original->isEqualTo($toCompare, true));
        $this->assertSame(true, $original->isNotEqualTo($toCompare, true));
    }

    public function testIsEqualToWithNull() : void
    {
        $original = NegativeFloatType::fromFloat(10.05);

        $this->assertFalse($original->isEqualTo(null, true), 'With strict check');
        $this->assertTrue($original->isNotEqualTo(null, true), 'With strict check');

        $this->assertFalse($original->isEqualTo(null, false), 'Without strict check');
        $this->assertTrue($original->isNotEqualTo(null, false), 'Without strict check');
    }

    public function testIsEqualToWithSameTypeSuccessful() : void
    {
        $instance1 = NegativeFloatType::fromFloat(10.05);
        $instance2 = NegativeFloatType::fromString('10.05');

        $this->assertTrue($instance1->isEqualTo($instance2, true), 'With strict check');
        $this->assertFalse($instance1->isNotEqualTo($instance2, true), 'With strict check');

        $this->assertTrue($instance1->isEqualTo($instance2, false), 'Without strict check');
        $this->assertFalse($instance1->isNotEqualTo($instance2, false), 'Without strict check');
    }

    public function testIsEqualToWithSameTypeUnsuccessfulDueToDifferenceInValue() : void
    {
        $instance1 = NegativeFloatType::fromFloat(10.05);
        $instance2 = NegativeFloatType::fromString('10');

        $this->assertFalse($instance1->isEqualTo($instance2, true), 'With strict check');
        $this->assertTrue($instance1->isNotEqualTo($instance2, true), 'With strict check');

        $this->assertFalse($instance1->isEqualTo($instance2, false), 'Without strict check');
        $this->assertTrue($instance1->isNotEqualTo($instance2, false), 'Without strict check');
    }
}