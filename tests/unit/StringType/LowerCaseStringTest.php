<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\StringType;

use FireMidge\Tests\ValueObject\Unit\Classes\LowerCaseStringType;
use FireMidge\ValueObject\Exception\InvalidValue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\LowerCaseStringType
 */
class LowerCaseStringTest extends TestCase
{
    public function validValueProvider() : array
    {
        return [
            [ ' UPPERCASE', 'uppercase' ],
            [ ' abcde', 'abcde' ],
            [ 'abcde ', 'abcde' ],
            [ 'someOtherString', 'someotherstring' ],
            [ 'and another one', 'and another one' ],
            [ '12345', '12345' ],
            [ '@abcd', '@abcd' ],
            [ '01234', '01234' ],
            [ 'O RGA', 'o rga' ],
        ];
    }

    /**
     * @dataProvider validValueProvider
     */
    public function testFromStringWithValidValue(string $raw, string $value) : void
    {
        $instance = LowerCaseStringType::fromString($raw);
        $this->assertSame($value, $instance->toString());
    }

    /**
     * @dataProvider validValueProvider
     */
    public function testFromStringOrNullWithValidValue(string $raw, string $value) : void
    {
        $instance = LowerCaseStringType::fromStringOrNull($value);
        $this->assertSame($value, $instance->toString());
    }

    public function invalidValueProvider() : array
    {
        return [
            [ ' UPPE', 'Value "uppe" is too short; must have 5 or more characters' ],
            [ '    ', 'Value "" is too short' ],
            [ 'abcd ', 'Value "abcd" is too short'  ],
            [ 'OR G', 'Value "or g" is too short'  ],
            [ '1234', 'Value "1234" is too short' ],
            [ 'UPPE ', 'Value "uppe" is too short' ],
        ];
    }

    /**
     * @dataProvider invalidValueProvider
     */
    public function testFromStringWithInvalidValue(string $value, string $expectedExceptionMessagePart) : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($expectedExceptionMessagePart);
        LowerCaseStringType::fromString($value);
    }

    /**
     * @dataProvider invalidValueProvider
     */
    public function testFromStringOrNullWithInvalidValue(string $value, string $expectedExceptionMessagePart) : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($expectedExceptionMessagePart);
        LowerCaseStringType::fromStringOrNull($value);
    }
}