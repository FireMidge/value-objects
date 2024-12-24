<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\StringType;

use FireMidge\Tests\ValueObject\Unit\Classes\EmailType;
use FireMidge\ValueObject\Exception\InvalidValue;
use FireMidge\ValueObject\Generic\Email;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(EmailType::class)]
class EmailTest extends TestCase
{
    public static function validValueProvider() : array
    {
        return [
            [ ' me@mine.com', 'me@mine.com' ],
            [ '  john.smith@gmail.co.uk', 'john.smith@gmail.co.uk' ],
            [ 'lady-MOON87@tiger.net  ', 'lady-moon87@tiger.net' ],
            [ 'HELLO@NO.ORG', 'hello@no.org' ],
        ];
    }

    #[DataProvider('validValueProvider')]
    public function testFromStringWithValidValue(string $raw, string $value) : void
    {
        $instance = EmailType::fromString($raw);
        $this->assertSame($value, $instance->toString());
    }

    #[DataProvider('validValueProvider')]
    public function testFromStringOrNullWithValidValue(string $raw, string $value) : void
    {
        $instance = EmailType::fromStringOrNull($value);
        $this->assertSame($value, $instance->toString());
    }

    public static function invalidValueProvider() : array
    {
        return [
            [ 'not@', 'E-mail address "not@" is invalid.' ],
            [ '--@--', 'E-mail address "--@--" is invalid.' ],
            [ 'hello(at)no.net', 'E-mail address "hello(at)no.net" is invalid.' ],
            [ '@something.co.uk', 'E-mail address "@something.co.uk" is invalid.' ],
            [ 'hello@net', 'E-mail address "hello@net" is invalid.' ],
            [ 'hello@localhost', 'E-mail address "hello@localhost" is invalid.' ],
            [ '  HELLO@localhost', 'E-mail address "hello@localhost" is invalid.' ],
        ];
    }

    #[DataProvider('invalidValueProvider')]
    public function testFromStringWithInvalidValue(string $value, string $expectedExceptionMessagePart) : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($expectedExceptionMessagePart);
        EmailType::fromString($value);
    }

    #[DataProvider('invalidValueProvider')]
    public function testFromStringOrNullWithInvalidValue(string $value, string $expectedExceptionMessagePart) : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($expectedExceptionMessagePart);
        EmailType::fromStringOrNull($value);
    }

    public function testSerialisesToJson() : void
    {
        $instance = Email::fromString('sample@example.com');
        $this->assertSame('"sample@example.com"', json_encode($instance));
    }
}