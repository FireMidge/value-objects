<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\StringType;

use FireMidge\Tests\ValueObject\Unit\Classes\SimpleNonConvertableObject;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleObject;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleStringType;
use FireMidge\Tests\ValueObject\Unit\Classes\StringEnumType;
use FireMidge\ValueObject\Exception\ConversionError;
use FireMidge\ValueObject\Exception\InvalidValue;
use LogicException;
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

    public function validateLengthSuccessfulProvider() : array
    {
        return [
          [ 'ÖSTERREICH', null, 10 ],
          [ 'Österreich', 9, 10 ],
          [ 'Österreich', 10, 10 ],
          [ 'Österreich', 10, null ],
          [ 'España', 1, null ],
          [ 'España', null, null ],
          [ 'España', 6, 6 ],
          [ 'España', 6, 6 ],
          [ 'España', 0, 6 ],
          [ 'España', 0, 10 ],
        ];
    }

    /**
     * @dataProvider validateLengthSuccessfulProvider
     */
    public function testValidateLengthSuccessful(string $value, ?int $minLength, ?int $maxLength) : void
    {
        SimpleStringType::fromString('')->validateLength($value, $minLength, $maxLength);
        $this->assertTrue(true, 'Expected validateLength not to throw an exception');
    }

    public function validateLengthExceptionProvider() : array
    {
        return [
            [ 'ÖSTERREICH', null, 9, 'Value "ÖSTERREICH" is too long; can only have a maximum length of 9 characters' ],
            [ 'ÖSTERREICH', null, 1, 'Value "ÖSTERREICH" is too long; can only have a maximum length of 1 character' ],
            [ 'Österreich', 11, null, 'Value "Österreich" is too short; must have 11 or more characters' ],
            [ '', 1, null, 'Value "" is too short; must have 1 or more characters' ],
            [ 'España', 7, 7, 'Value "España" is invalid; must have exactly 7 characters' ],
            [ '', 1, 1, 'Value "" is invalid; must have exactly 1 character' ],
            [ 'España', 7, 10, 'Value "España" is invalid. Length must be between 7 and 10 characters' ],
            [ 'España', 0, 1, 'Value "España" is invalid. Length must be between 0 and 1 characters' ],
            [ 'España', 0, 2, 'Value "España" is invalid. Length must be between 0 and 2 characters' ],
        ];
    }

    /**
     * @dataProvider validateLengthExceptionProvider
     */
    public function testValidateLengthThrowsException(
        string $value,
        ?int $minLength,
        ?int $maxLength,
        string $expectedMessage
    ) : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($expectedMessage);

        SimpleStringType::fromString('')->validateLength($value, $minLength, $maxLength);
    }

    public function testValidateLengthThrowsLogicException() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(
            'Trying to validate length of a string, '
            . 'but the required $minLength "10" is higher than $maxLength "9"'
        );

        SimpleStringType::fromString('')->validateLength('Hola', 10, 9);
    }

    public function validEmailProvider() : array
    {
        return [
            [ 'me@mine.com' ],
            [ 'john.smith@gmail.co.uk' ],
            [ 'lady-moon87@tiger.net' ],
            [ 'hello@no.org' ],
            [ 'HELLO@no.org' ],
        ];
    }

    /**
     * @dataProvider validEmailProvider
     */
    public function testValidateEmailAddressSuccessful(string $value) : void
    {
        SimpleStringType::fromString('')->validateEmailAddress($value);
        $this->assertTrue(true, 'Expected validateEmailAddress not to throw an exception');
    }

    public function invalidEmailProvider() : array
    {
        return [
            [ 'not@', 'E-mail address "not@" is invalid.' ],
            [ '--@--', 'E-mail address "--@--" is invalid.' ],
            [ 'hello(at)no.net', 'E-mail address "hello(at)no.net" is invalid.' ],
            [ '@something.co.uk', 'E-mail address "@something.co.uk" is invalid.' ],
            [ 'hello@net', 'E-mail address "hello@net" is invalid.' ],
            [ 'hello@localhost', 'E-mail address "hello@localhost" is invalid.' ],
            [ 'HELLO@localhost', 'E-mail address "HELLO@localhost" is invalid.' ],
            [ '  john.smith@gmail.co.uk', 'E-mail address "  john.smith@gmail.co.uk" is invalid.' ],
            [ 'john.smith@gmail.co.uk ', 'E-mail address "john.smith@gmail.co.uk " is invalid.' ],
        ];
    }

    /**
     * @dataProvider invalidEmailProvider
     */
    public function testValidateEmailAddressThrowsLogicException(string $value, string $expectedErrorMessage) : void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($expectedErrorMessage);

        SimpleStringType::fromString('')->validateEmailAddress($value);
    }

    public function testIsEqualWithSameTypeSuccessful() : void
    {
        $instance1 = SimpleStringType::fromString('Arnø Åklænd');
        $instance2 = SimpleStringType::fromString('Arnø Åklænd');

        $this->assertTrue($instance1->isEqualTo($instance2, false));
        $this->assertFalse($instance1->isNotEqualTo($instance2, false));

        $this->assertTrue($instance1->isEqualTo($instance2), 'isEqualTo with strict check');
        $this->assertFalse($instance2->isNotEqualTo($instance1), 'isNotEqualTo with strict check');
    }

    public function testIsEqualUnsuccessfulWithDifferentCharSets() : void
    {
        $instance1 = SimpleStringType::fromString('Arnø Åklænd');
        $instance2 = SimpleStringType::fromString('Arno Akland');

        $this->assertFalse($instance1->isEqualTo($instance2, false));
        $this->assertTrue($instance1->isNotEqualTo($instance2, false));

        $this->assertFalse($instance1->isEqualTo($instance2), 'isEqualTo with strict check');
        $this->assertTrue($instance1->isNotEqualTo($instance2), 'isNotEqualTo with strict check');
    }

    public function successfulLooseCheckComparisonsProvider() : array
    {
        return [
            [ StringEnumType::spring() ],
            [ 'spring' ],
            [ new SimpleObject('spring') ],
        ];
    }

    /**
     * @dataProvider successfulLooseCheckComparisonsProvider
     */
    public function testEqualsToOnlyWithLooseCheckSuccessful(mixed $other) : void
    {
        $instance1 = SimpleStringType::fromString('spring');
        $instance2 = $other;

        $this->assertTrue($instance1->isEqualTo($instance2, false));
        $this->assertFalse($instance1->isNotEqualTo($instance2, false));

        $this->assertFalse($instance1->isEqualTo($instance2), 'isEqualTo with strict check');
        $this->assertTrue($instance1->isNotEqualTo($instance2), 'isNotEqualTo with strict check');
    }

    public function unsuccessfulLooseCheckComparisonsProvider() : array
    {
        return [
            [ 'Spring' ],
            [ ' spring' ],
            [ 'spring ' ],
            [ '1' ],
            [ 'true' ],
            [ null ],
            [ new SimpleObject('Spring') ],
        ];
    }

    /**
     * @dataProvider unsuccessfulLooseCheckComparisonsProvider
     */
    public function testIsEqualEvenWithLooseCheckUnsuccessful(mixed $other) : void
    {
        $instance1 = SimpleStringType::fromString('spring');
        $instance2 = $other;

        $this->assertFalse($instance1->isEqualTo($instance2, false));
        $this->assertTrue($instance1->isNotEqualTo($instance2, false));

        $this->assertFalse($instance1->isEqualTo($instance2), 'isEqualTo with strict check');
        $this->assertTrue($instance1->isNotEqualTo($instance2), 'isNotEqualTo with strict check');
    }

    public function testConversionErrorTriggered() : void
    {
        $instance1 = SimpleStringType::fromString('spring');
        $instance2 = new SimpleNonConvertableObject('spring');

        $this->expectException(ConversionError::class);
        $this->expectExceptionMessage(sprintf(
            'Could not convert value of type %s to string. Make sure the class has one of these methods: ',
            SimpleNonConvertableObject::class
        ));

        $instance1->isEqualTo($instance2, false);
    }
}