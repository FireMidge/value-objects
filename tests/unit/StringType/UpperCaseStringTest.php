<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\StringType;

use FireMidge\Tests\ValueObject\Unit\Classes\UpperCaseStringType;
use FireMidge\ValueObject\Exception\InvalidValue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\UpperCaseStringType
 */
class UpperCaseStringTest extends TestCase
{
    public function validValueProvider() : array
    {
        return [
            [ ' at', 'AT' ],
            [ 'es ', 'ES' ],
            [ '    uk ', 'UK' ],
            [ '@a', '@A' ],
            [ '3@         ', '3@' ],
            [ '-a', '-A' ],
        ];
    }

    /**
     * @dataProvider validValueProvider
     */
    public function testFromStringWithValidValue(string $raw, string $value) : void
    {
        $instance = UpperCaseStringType::fromString($raw);
        $this->assertSame($value, $instance->toString());
    }

    /**
     * @dataProvider validValueProvider
     */
    public function testFromStringOrNullWithValidValue(string $raw, string $value) : void
    {
        $instance = UpperCaseStringType::fromStringOrNull($value);
        $this->assertSame($value, $instance->toString());
    }

    public function invalidValueProvider() : array
    {
        return [
            [ ' AUT', 'Value "AUT" is too long; can only have a maximum length of 2 characters' ],
            [ '    AUS', 'Value "AUS" is too long' ],
            [ 'AU- ', 'Value "AU-" is too long'  ],
            [ 'O gr', 'Value "O GR" is too long'  ],
            [ '1234', 'Value "1234" is too long' ],
        ];
    }

    /**
     * @dataProvider invalidValueProvider
     */
    public function testFromStringWithInvalidValue(string $value, string $expectedExceptionMessagePart) : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($expectedExceptionMessagePart);
        UpperCaseStringType::fromString($value);
    }

    /**
     * @dataProvider invalidValueProvider
     */
    public function testFromStringOrNullWithInvalidValue(string $value, string $expectedExceptionMessagePart) : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($expectedExceptionMessagePart);
        UpperCaseStringType::fromStringOrNull($value);
    }
}