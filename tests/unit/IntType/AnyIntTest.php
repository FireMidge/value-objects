<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\IntType;

use FireMidge\ValueObject\Generic\AnyInteger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AnyInteger::class)]
class AnyIntTest extends TestCase
{
    public function testSerialisesToJson() : void
    {
        $instance = AnyInteger::fromInt(123);
        $this->assertSame('123', json_encode($instance));
    }
}