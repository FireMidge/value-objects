<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\FloatType;

use FireMidge\ValueObject\Generic\AnyFloat;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AnyFloat::class)]
class AnyFloatTest extends TestCase
{
    public function testSerialisesToJson() : void
    {
        $instance = AnyFloat::fromNumber(30.56999);
        $this->assertSame('30.56999', json_encode($instance));
    }
}