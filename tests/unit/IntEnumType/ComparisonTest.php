<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\IntEnumType;

use FireMidge\Tests\ValueObject\Unit\Classes\DoubleObject;
use FireMidge\Tests\ValueObject\Unit\Classes\IntEnumType;
use FireMidge\Tests\ValueObject\Unit\Classes\NumberObject;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleFloatType;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleIntType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IntEnumType
 */
class ComparisonTest extends TestCase
{

    public function looselyEqualDataProvider() : array
    {
        return [
            [ 1 ],
            [ 1.9 ],
            [ SimpleIntType::fromInt(1) ],
            [ SimpleFloatType::fromFloat(1.0) ],
            [ SimpleFloatType::fromFloat(1.9) ],
            [ new NumberObject(1.0) ],
            [ new NumberObject(1.8) ],
            [ new DoubleObject(1.0) ],
            [ new DoubleObject(1.9) ],
        ];
    }

    /**
     * @dataProvider looselyEqualDataProvider
     */
    public function testIsEqualToWithStrictCheckUnsuccessful(mixed $toCompare) : void
    {
        $original = IntEnumType::fromInt(1);
        $this->assertSame(false, $original->isEqualTo($toCompare, true));
        $this->assertSame(true, $original->isNotEqualTo($toCompare, true));
    }

    /**
     * @dataProvider looselyEqualDataProvider
     */
    public function testIsEqualToWithLooseCheckSuccessful(mixed $toCompare) : void
    {
        $original = IntEnumType::fromInt(1);
        $this->assertTrue($original->isEqualTo($toCompare, false));
        $this->assertFalse($original->isNotEqualTo($toCompare, false));
    }

    public function testIsEqualToWithNull() : void
    {
        $original = IntEnumType::fromInt(4);

        $this->assertFalse($original->isEqualTo(null, true), 'With strict check');
        $this->assertTrue($original->isNotEqualTo(null, true), 'With strict check');

        $this->assertFalse($original->isEqualTo(null, false), 'Without strict check');
        $this->assertTrue($original->isNotEqualTo(null, false), 'Without strict check');
    }

    public function testIsEqualToWithSameTypeSuccessful() : void
    {
        $instance1 = IntEnumType::fromInt(4);
        $instance2 = IntEnumType::fromString('4');

        $this->assertTrue($instance1->isEqualTo($instance2, true), 'With strict check');
        $this->assertFalse($instance1->isNotEqualTo($instance2, true), 'With strict check');

        $this->assertTrue($instance1->isEqualTo($instance2, false), 'Without strict check');
        $this->assertFalse($instance1->isNotEqualTo($instance2, false), 'Without strict check');
    }
}