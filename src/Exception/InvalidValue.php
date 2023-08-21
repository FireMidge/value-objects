<?php
declare(strict_types=1);

namespace FireMidge\ValueObject\Exception;

use OutOfBoundsException;
use Throwable;

/**
 * Exception used by value objects, if a value is passed that is not one of the enumerated valid ones.
 */
class InvalidValue extends OutOfBoundsException
{
    use RendersValue;

    public static function valueNotOneOfEnum(
        $value,
        array $validValues,
        int $code = 0,
        ?Throwable $previous = null
    ) : static
    {
        return new static(
            sprintf(
                'Value "%s" is invalid. Must be one of: "%s"',
                (string) $value,
                implode('", "', $validValues)
            ),
            $code,
            $previous
        );
    }

    public static function valuesNotOfEnum(
        array $values,
        array $validValues,
        int $code = 0,
        ?Throwable $previous = null
    ) : static
    {
        return new static(
            sprintf(
                'The following values are not valid: "%s". Valid values are: "%s"',
                implode('", "', $values),
                implode('", "', $validValues)
            ),
            $code,
            $previous
        );
    }

    public static function invalidValue(
        $value,
        ?string $message = null,
        int $code = 0,
        ?Throwable $previous = null
    ) : static
    {
        return new static(
            sprintf(
                'Value "%s" is invalid.%s',
                (string) $value,
                $message === null ? '' : sprintf(' (%s)', $message)
            ),
            $code,
            $previous
        );
    }

    public static function valueTooShort(
        string $value,
        int $minimumCharacterLength,
        ?string $message = null,
        int $code = 0,
        ?Throwable $previous = null
    ) : static
    {
        $message = $minimumCharacterLength === 0
            ? sprintf(
                'Value must not be empty.%s',
                $message === null ? '' : sprintf(' (%s)', $message)
            )
            : sprintf(
                'Value "%s" is too short; must have %d or more characters.%s',
                $value,
                $minimumCharacterLength,
                $message === null ? '' : sprintf(' (%s)', $message)
            );

        return new static($message, $code, $previous);
    }

    public static function valueTooLong(
        string $value,
        int $maximumCharacterLength,
        ?string $message = null,
        int $code = 0,
        ?Throwable $previous = null
    ) : static
    {
        $message = sprintf(
                'Value "%s" is too long; can only have a maximum length of %d character%s.%s',
                $value,
                $maximumCharacterLength,
                $maximumCharacterLength === 1 ? '' : 's',
                $message === null ? '' : sprintf(' (%s)', $message)
            );

        return new static($message, $code, $previous);
    }

    public static function valueLengthNotBetween(
        string $value,
        int $minimumCharacterLength,
        int $maximumCharacterLength,
        ?string $message = null,
        int $code = 0,
        ?Throwable $previous = null
    ) : static
    {
        $message = $minimumCharacterLength === $maximumCharacterLength
            ? sprintf(
                'Value "%s" is invalid; must have exactly %d character%s.%s',
                $value,
                $maximumCharacterLength,
                $maximumCharacterLength === 1 ? '' : 's',
                $message === null ? '' : sprintf(' (%s)', $message)
            )
            : sprintf(
                'Value "%s" is invalid. Length must be between %d and %d characters.%s',
                $value,
                $minimumCharacterLength,
                $maximumCharacterLength,
                $message === null ? '' : sprintf(' (%s)', $message)
            );

        return new static($message, $code, $previous);
    }

    public static function valueTooLow(
        float|int $value,
        float|int $minimumValue,
        ?string $message = null,
        int $code = 0,
        ?Throwable $previous = null
    ) : static
    {
        $message = $minimumValue == 0 // If we were to do a strict comparison here, it would fail for anything but integer
            ? sprintf(
                'Value must be a positive number, value provided is %s.%s',
                (string) $value,
                $message === null ? '' : sprintf(' (%s)', $message)
            )
            : sprintf(
                'Value must be higher than or equal to %s, value provided is %s.%s',
                (string) $minimumValue,
                (string) $value,
                $message === null ? '' : sprintf(' (%s)', $message)
            );

        return new static($message, $code, $previous);
    }

    public static function valueTooHigh(
        float|int $value,
        float|int $maximumValue,
        ?string $message = null,
        int $code = 0,
        ?Throwable $previous = null
    ) : static
    {
        return new static(
            sprintf(
                'Value must be lower than or equal to %s, value provided is %s.%s',
                (string) $maximumValue,
                (string) $value,
                $message === null ? '' : sprintf(' (%s)', $message)
            ),
            $code,
            $previous
        );
    }

    public static function invalidValues(array $invalidValues, int $code = 0, ?Throwable $previous = null) : static
    {
        return new static(
            sprintf('The following values are invalid: "%s"', implode('", "', $invalidValues)),
            $code,
            $previous
        );
    }

    public static function notInstanceOf($value, string $class, int $code = 0, ?Throwable $previous = null) : static
    {
        $message = is_object($value)
            ? sprintf(
                'Invalid value. Must be an instance of "%s", but is "%s"',
                $class,
                get_class($value)
            )
            : sprintf(
                'Invalid value. Must be an object and an instance of "%s"',
                $class
            );

        return new static(
            $message,
            $code,
            $previous
        );
    }

    public static function invalidType($value, string $type, int $code = 0, ?Throwable $previous = null) : static
    {
        return new static(
            sprintf(
                'Invalid value. Must be of type "%s" but got "%s"',
                $type,
                gettype($value)
            ),
            $code,
            $previous
        );
    }

    public static function notAnEmailAddress(
        string $value,
        ?string $message = null,
        int $code = 0,
        ?Throwable $previous = null
    ) : static
    {
        return new static(
            sprintf(
                'E-mail address "%s" is invalid.%s',
                $value,
                $message === null ? '' : sprintf(' (%s)', $message)
            ),
            $code,
            $previous
        );
    }
}