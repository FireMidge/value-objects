<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\StringType;

use FireMidge\Tests\ValueObject\Unit\Classes\SimpleStringType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\SimpleStringType
 */
class SimpleStringTest extends TestCase
{
    public function validValueProvider() : array
    {
        return [
            [ '' ],
            [ ' ' ],
            [ 'a' ],
            [ 'ü' ],
            [ '()' ],
            [ '{}' ],
            [ '<>' ],
            [ '$' ],
            [ '@' ],
            [ '!?.' ],
            [ 'someOtherString' ],
            [ 'and another one' ],
            [ 'trailing white space ' ],
            [ '0' ],
            [ '0Hello' ],
            [ '01' ],
        ];
    }

    /**
     * @dataProvider validValueProvider
     */
    public function testFromStringWithValidValue(string $value) : void
    {
        $instance = SimpleStringType::fromString($value);
        $this->assertSame($value, $instance->toString());
    }

    /**
     * @dataProvider validValueProvider
     */
    public function testFromStringOrNullWithValidValue(string $value) : void
    {
        $instance = SimpleStringType::fromStringOrNull($value);
        $this->assertSame($value, $instance->toString());
    }

    public function testFromStringOrNullWithNull() : void
    {
        $instance = SimpleStringType::fromStringOrNull(null);
        $this->assertNull($instance);
    }

    /**
     * @dataProvider validValueProvider
     */
    public function testMagicToString(string $value) : void
    {
        $instance = SimpleStringType::fromString($value);
        $this->assertEquals($value, $instance);
    }
}