<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\FloatType;

use FireMidge\Tests\ValueObject\Unit\Classes\MaxOnlyFloatType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MaxOnlyFloatType::class)]
class MaxOnlyFloatTest extends TestCase
{
    public function testMaximumErrorMessage() : void
    {
        $instance = MaxOnlyFloatType::fromFloat(77.777);

        $this->expectExceptionMessage(
            'Cannot add value 11.112 to 77.777 as it brings the total (88.889) above the maximum value of 88.888'
        );
        $instance->add(11.112);
    }

    public function testCanAddUntilMaximumValue() : void
    {
        $instance = MaxOnlyFloatType::fromFloat(77.777);
        $result = $instance->add(11.111);

        $this->assertSame(88.888, $result->toFloat());
    }

    public function testCanSubtractUntilMaximumValue() : void
    {
        $instance = MaxOnlyFloatType::fromFloat(77.777);
        $result = $instance->subtract(-11.111);

        $this->assertSame(88.888, $result->toFloat());
    }
}