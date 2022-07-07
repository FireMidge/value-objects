<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\StringType;

use FireMidge\Tests\ValueObject\Unit\Classes\SimpleStringType;
use PHPUnit\Framework\TestCase;

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
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\SimpleStringType::fromString
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\SimpleStringType::toString
     */
    public function testFromStringWithValidValue(string $value) : void
    {
        $instance = SimpleStringType::fromString($value);
        $this->assertSame($value, $instance->toString());
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\SimpleStringType::fromStringOrNull
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\SimpleStringType::toString
     */
    public function testFromStringOrNullWithValidValue(string $value) : void
    {
        $instance = SimpleStringType::fromStringOrNull($value);
        $this->assertSame($value, $instance->toString());
    }

    /**
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\SimpleStringType::fromStringOrNull
     */
    public function testFromStringOrNullWithNull() : void
    {
        $instance = SimpleStringType::fromStringOrNull(null);
        $this->assertNull($instance);
    }

    /**
     * @dataProvider validValueProvider
     *
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\SimpleStringType::__toString
     * @covers \FireMidge\Tests\ValueObject\Unit\Classes\SimpleStringType::fromString
     */
    public function testMagicToString(string $value) : void
    {
        $instance = SimpleStringType::fromString($value);
        $this->assertEquals($value, $instance);
    }
}