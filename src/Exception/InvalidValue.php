<?php
declare(strict_types=1);

namespace FireMidge\ValueObject\Exception;

use Throwable;

/**
 * Exception used by value objects, if a value is passed that is not one of the enumerated valid ones.
 */
class InvalidValue extends \OutOfBoundsException
{
    public static function valueNotOneOfEnum(
        $value,
        array $validValues,
        int $code = 0,
        ?Throwable $previous = null
    ) : self
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

    public static function invalidValue(
        $value,
        ?string $message = null,
        int $code = 0,
        ?Throwable $previous = null
    ) : self
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

    /**
     * @param float|int|double $value
     * @param float|int|double $minimumValue
     *
     * @return static
     */
    public static function valueTooLow(
        $value,
        $minimumValue,
        ?string $message = null,
        int $code = 0,
        ?Throwable $previous = null
    ) : self
    {
        return new static(
            sprintf(
                'Value must be higher than %s, value provided is %s.%s',
                (string) $minimumValue,
                (string) $value,
                $message === null ? '' : sprintf(' (%s)', $message)
            ),
            $code,
            $previous
        );
    }

    /**
     * @param float|int|double $value
     * @param float|int|double $maximumValue
     *
     * @return static
     */
    public static function valueTooHigh(
        $value,
        $maximumValue,
        ?string $message = null,
        int $code = 0,
        ?Throwable $previous = null
    ) : self
    {
        return new static(
            sprintf(
                'Value must be lower than %s, value provided is %s.%s',
                (string) $maximumValue,
                (string) $value,
                $message === null ? '' : sprintf(' (%s)', $message)
            ),
            $code,
            $previous
        );
    }

    public static function invalidValues(array $invalidValues, int $code = 0, ?Throwable $previous = null) : self
    {
        return new static(
            sprintf('The following values are invalid: "%s"', implode('", "', $invalidValues)),
            $code,
            $previous
        );
    }
}