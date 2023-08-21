<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\IntType;

use FireMidge\Tests\ValueObject\Unit\Classes\DoubleObject;
use FireMidge\Tests\ValueObject\Unit\Classes\NegativeIntType;
use FireMidge\Tests\ValueObject\Unit\Classes\NumberObject;
use FireMidge\Tests\ValueObject\Unit\Classes\OddIntType;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleFloatType;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleIntType;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleStringType;
use FireMidge\ValueObject\Exception\ConversionError;
use PHPUnit\Framework\TestCase;

/**
 * Tests calculation operations on Int type classes.
 *
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\SimpleIntType
 */
class OperationsTest extends TestCase
{
    public function successfulAddOperationsProvider() : array
    {
        return [
            [ 5, 20 ],
            [ 5.9, 20 ], // Floats are supposed to be stripped of decimals when converting to integers
            [ SimpleIntType::fromInt(9), 24 ],
            [ OddIntType::fromInt(3), 18 ],
            [ SimpleFloatType::fromFloat(3.8), 18 ],
            [ new NumberObject(10.5), 25 ],
            [ new NumberObject(11), 26 ],
            [ new DoubleObject(17.01), 32 ],
        ];
    }

    /**
     * @dataProvider successfulAddOperationsProvider
     */
    public function testAddSuccessful(mixed $thingToAdd, int $expectedResult) : void
    {
        $original = SimpleIntType::fromInt(15);
        $new      = $original->add($thingToAdd);

        $this->assertSame(15, $original->toInt(), 'Did not expect the original to be updated.');
        $this->assertSame($expectedResult, $new->toInt(), 'New instance does not match expected.');
    }

    public function testAddUnsuccessful() : void
    {
        $this->expectException(ConversionError::class);
        $this->expectExceptionMessage('Could not convert value "hello" to int. Make sure the class has one of these methods: "toInt", "toFloat", "toDouble", "toNumber"');

        SimpleIntType::fromInt(20)->add(SimpleStringType::fromString('hello'));
    }

    public function successfulSubtractOperationsProvider() : array
    {
        return [
            [ 5, 10 ],
            [ 5.9, 10 ], // Floats are supposed to be stripped of decimals when converting to integers
            [ SimpleIntType::fromInt(9), 6 ],
            [ OddIntType::fromInt(3), 12 ],
            [ SimpleFloatType::fromFloat(3.8), 12 ],
            [ new NumberObject(10.5), 5 ],
            [ new NumberObject(11), 4 ],
            [ new DoubleObject(17.01), -2 ],
        ];
    }

    /**
     * @dataProvider successfulSubtractOperationsProvider
     */
    public function testSubtractSuccessful(mixed $thingToSubtract, int $expectedResult) : void
    {
        $original = NegativeIntType::fromInt(15);
        $new      = $original->subtract($thingToSubtract);

        $this->assertSame(15, $original->toInt(), 'Did not expect the original to be updated.');
        $this->assertSame($expectedResult, $new->toInt(), 'New instance does not match expected.');
    }

    public function successfulGreaterThanComparisonProvider() : array
    {
        return [
            [ 5, true ],
            [ 5.9, true ], // Floats are supposed to be stripped of decimals when converting to integers
            [ 11, false ],
            [ 11.9, false ],
            [ SimpleIntType::fromInt(9), true ],
            [ SimpleIntType::fromInt(11), false ],
            [ OddIntType::fromInt(9), true ],
            [ OddIntType::fromInt(11), false ],
            [ SimpleFloatType::fromFloat(9.9), true ],
            [ SimpleFloatType::fromFloat(11.1), false ],
            [ new NumberObject(9.9), true ],
            [ new NumberObject(11.0), false ],
            [ new NumberObject(9), true ],
            [ new NumberObject(11), false ],
            [ new DoubleObject(9.09), true ],
            [ new DoubleObject(11.0), false ],
        ];
    }

    /**
     * @dataProvider successfulGreaterThanComparisonProvider
     */
    public function testGreaterThanComparisonSuccessful(mixed $toCompare, bool $expectedResult) : void
    {
        $original = NegativeIntType::fromInt(10);
        $this->assertSame($expectedResult, $original->isGreaterThan($toCompare));
    }

    /**
     * @dataProvider successfulGreaterThanComparisonProvider
     */
    public function testLessThanComparisonSuccessful(mixed $toCompare, bool $expectedResult) : void
    {
        $original = NegativeIntType::fromInt(10);
        $this->assertSame(! $expectedResult, $original->isLessThan($toCompare));
    }

    public function equalDataProvider() : array
    {
        return [
            [ 10 ],
            [ 10.9 ],
            [ SimpleIntType::fromInt(10) ],
            [ SimpleFloatType::fromFloat(10.0) ],
            [ SimpleFloatType::fromFloat(10.9) ],
            [ new NumberObject(10.0) ],
            [ new NumberObject(10.8) ],
            [ new DoubleObject(10.0) ],
            [ new DoubleObject(10.9) ],
        ];
    }

    /**
     * @dataProvider equalDataProvider
     */
    public function testGreaterThanComparisonWithEqualValue(mixed $toCompare) : void
    {
        $original = NegativeIntType::fromInt(10);
        $this->assertSame(false, $original->isGreaterThan($toCompare));
    }

    /**
     * @dataProvider equalDataProvider
     */
    public function testLessThanComparisonWithEqualValue(mixed $toCompare) : void
    {
        $original = NegativeIntType::fromInt(10);
        $this->assertSame(false, $original->isLessThan($toCompare));
    }

    /**
     * @dataProvider equalDataProvider
     */
    public function testGreaterThanOrEqualToComparisonWithEqualValue(mixed $toCompare) : void
    {
        $original = NegativeIntType::fromInt(10);
        $this->assertSame(true, $original->isGreaterThanOrEqualTo($toCompare));
    }

    /**
     * @dataProvider equalDataProvider
     */
    public function testLessThanOrEqualToComparisonWithEqualValue(mixed $toCompare) : void
    {
        $original = NegativeIntType::fromInt(10);
        $this->assertSame(true, $original->isLessThanOrEqualTo($toCompare));
    }

    /**
     * @dataProvider equalDataProvider
     */
    public function testIsEqualToWithStrictCheckUnsuccessful(mixed $toCompare) : void
    {
        $original = NegativeIntType::fromInt(10);
        $this->assertSame(false, $original->isEqualTo($toCompare, true));
        $this->assertSame(true, $original->isNotEqualTo($toCompare, true));
    }


    /**
     * @dataProvider equalDataProvider
     */
    public function testIsEqualToWithLooseCheckSuccessful(mixed $toCompare) : void
    {
        $original = NegativeIntType::fromInt(10);
        $this->assertTrue($original->isEqualTo($toCompare, false));
        $this->assertFalse($original->isNotEqualTo($toCompare, false));
    }

    public function testIsEqualToWithNull() : void
    {
        $original = NegativeIntType::fromInt(10);

        $this->assertFalse($original->isEqualTo(null, true), 'With strict check');
        $this->assertTrue($original->isNotEqualTo(null, true), 'With strict check');

        $this->assertFalse($original->isEqualTo(null, false), 'Without strict check');
        $this->assertTrue($original->isNotEqualTo(null, false), 'Without strict check');
    }

    public function testIsEqualToWithSameTypeSuccessful() : void
    {
        $instance1 = NegativeIntType::fromInt(10);
        $instance2 = NegativeIntType::fromString('10');

        $this->assertTrue($instance1->isEqualTo($instance2, true), 'With strict check');
        $this->assertFalse($instance1->isNotEqualTo($instance2, true), 'With strict check');

        $this->assertTrue($instance1->isEqualTo($instance2, false), 'Without strict check');
        $this->assertFalse($instance1->isNotEqualTo($instance2, false), 'Without strict check');
    }
}