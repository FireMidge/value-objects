<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\IntType;

use FireMidge\Tests\ValueObject\Unit\Classes\MaxOnlyIntType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MaxOnlyIntType::class)]
class MaxOnlyIntTest extends TestCase
{
    public function testMaximumErrorMessage() : void
    {
        $instance = MaxOnlyIntType::fromInt(-20);

        $this->expectExceptionMessage(
            'Cannot add value 20 to -20 as it brings the total (0) above the maximum value of -1'
        );
        $instance->add(20);
    }

    public function testCanAddUntilMaximumValue() : void
    {
        $instance = MaxOnlyIntType::fromInt(-20);
        $result = $instance->add(19);

        $this->assertSame(-1, $result->toInt());
    }

    public function testCanSubtractUntilMaximumValue() : void
    {
        $instance = MaxOnlyIntType::fromInt(-20);
        $result = $instance->subtract(-19);

        $this->assertSame(-1, $result->toInt());
    }
}