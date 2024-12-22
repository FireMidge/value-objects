<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\FloatType;

use FireMidge\ValueObject\Generic\Percentage;
use PHPUnit\Framework\TestCase;

/**
 * @covers \FireMidge\ValueObject\Generic\Percentage
 */
class PercentageTest extends TestCase
{
    public function testMaximumErrorMessage() : void
    {
        $per = Percentage::fromFloat(70.5);

        $this->expectExceptionMessage(
            'Cannot add value 30 to 70.5 as it brings the total (100.5) above the maximum value of 100'
        );
        $per->add(30);
    }

    public function testMinimumErrorMessage() : void
    {
        $per = Percentage::fromFloat(20);

        $this->expectExceptionMessage(
            'Cannot subtract value 25 from 20 as it brings the total (-5) below the minimum value of 0'
        );
        $per->subtract(25);
    }

    public function testMinimumErrorMessageWithAdd() : void
    {
        $per = Percentage::fromFloat(70.5);

        $this->expectExceptionMessage(
            'Cannot add value -80 to 70.5 as it brings the total (-9.5) below the minimum value of 0'
        );
        $per->add(-80);
    }

    public function testMaximumErrorMessageWithSubtract() : void
    {
        $per = Percentage::fromFloat(89.99);

        $this->expectExceptionMessage(
            'Cannot subtract value -21.5 from 89.99 as it brings the total (111.49) above the maximum value of 100'
        );
        $per->subtract(-21.5);
    }

    public function testToString() : void
    {
        $per = Percentage::fromFloat(70.5);
        $this->assertSame('71%', $per->toString());
        $this->assertSame(71, $per->toInt());

        $per2 = $per->add(2.25);
        $this->assertSame(72.75, $per2->toFloat());
        $this->assertSame('73%', $per2->toString());
        $this->assertSame(73, $per2->toInt());

        $per3 = $per2->subtract(0.755);
        $this->assertSame(71.995, $per3->toFloat());
        $this->assertSame('72%', $per3->toString());
        $this->assertSame(72, $per3->toInt());
    }

    public function testRoundingDownWhenToString() : void
    {
        $per = Percentage::fromFloat(70.49);
        $this->assertSame('70%', $per->toString());
        $this->assertSame(70, $per->toInt());
    }
}