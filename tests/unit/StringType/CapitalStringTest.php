<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\StringType;

use FireMidge\Tests\ValueObject\Unit\Classes\CapitalStringType;
use FireMidge\ValueObject\Exception\InvalidValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(CapitalStringType::class)]
class CapitalStringTest extends TestCase
{
    public static function validValueProvider() : array
    {
        return [
            [ ' at', 'At' ],
            [ ' aut', 'Aut' ],
            [ 'es ', 'Es' ],
            [ 'esp ', 'Esp' ],
            [ '    uk ', 'Uk' ],
            [ '    gbr ', 'Gbr' ],
            [ '@a', '@a' ],
            [ '3@         ', '3@' ],
            [ '-a', '-a' ],
            [ '-a-', '-a-' ],
            [ '---', '---' ],
            [ 'ÜÄö', 'Üäö' ],
            [ 'áýÚ', 'Áýú' ],
        ];
    }

    #[DataProvider('validValueProvider')]
    public function testFromStringWithValidValue(string $raw, string $value) : void
    {
        $instance = CapitalStringType::fromString($raw);
        $this->assertSame($value, $instance->toString());
    }

    #[DataProvider('validValueProvider')]
    public function testFromStringOrNullWithValidValue(string $raw, string $value) : void
    {
        $instance = CapitalStringType::fromStringOrNull($value);
        $this->assertSame($value, $instance->toString());
    }

    public static function invalidValueProvider() : array
    {
        return [
            [ ' AUTR', 'Value "Autr" is invalid. Length must be between 2 and 3 characters.' ],
            [ '    AUST', 'Value "Aust" is invalid' ],
            [ 'AU-- ', 'Value "Au--" is invalid'  ],
            [ 'O gri', 'Value "O gri" is invalid'  ],
            [ '1234', 'Value "1234" is invalid' ],
        ];
    }

    #[DataProvider('invalidValueProvider')]
    public function testFromStringWithInvalidValue(string $value, string $expectedExceptionMessagePart) : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($expectedExceptionMessagePart);
        CapitalStringType::fromString($value);
    }

    #[DataProvider('invalidValueProvider')]
    public function testFromStringOrNullWithInvalidValue(string $value, string $expectedExceptionMessagePart) : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($expectedExceptionMessagePart);
        CapitalStringType::fromStringOrNull($value);
    }
}