<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit;

use FireMidge\Tests\ValueObject\Unit\classes\NegativeIntType;
use PHPUnit\Framework\TestCase;

class NegativeIntTest extends TestCase
{
    public function validValueProvider() : array
    {
        return [
            [ 0 ],
            [ 1 ],
            [ 700 ],
            [ 58760295 ],
            [ -1 ],
            [ -20 ],
        ];
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\classes\NegativeIntType::fromInt
     * @covers \FireMidge\Tests\ValueObject\Unit\classes\NegativeIntType::toInt
     */
    public function testFromIntWithValidValue(int $value) : void
    {
        $instance = NegativeIntType::fromInt($value);
        $this->assertSame($value, $instance->toInt());
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\classes\NegativeIntType::fromIntOrNull
     * @covers \FireMidge\Tests\ValueObject\Unit\classes\NegativeIntType::toInt
     */
    public function testFromIntOrNullWithValidValue(int $value) : void
    {
        $instance = NegativeIntType::fromIntOrNull($value);
        $this->assertSame($value, $instance->toInt());
    }
}