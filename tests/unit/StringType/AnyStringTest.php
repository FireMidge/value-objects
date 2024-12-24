<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\StringType;

use FireMidge\ValueObject\Generic\AnyString;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AnyString::class)]
class AnyStringTest extends TestCase
{
    public function testSerialisesToJson() : void
    {
        $instance = AnyString::fromString('^&505AAA+D');
        $this->assertSame('"^&505AAA+D"', json_encode($instance));
    }
}