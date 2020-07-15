<?php
declare(strict_types=1);

namespace FireMidge\ValueObject\Exception;

/**
 * Exception used by value objects, if a value is passed that is not one of the enumerated valid ones.
 */
class InvalidValue extends \OutOfBoundsException
{
    public static function valueNotOneOfEnum(
        $value,
        array $validValues,
        int $code = 0,
        ?\Throwable $previous = null
    ) : self
    {
        return new static(
            sprintf('Value "%s" is invalid. Must be one of: "%s"', (string) $value, implode('", "', $validValues)),
            $code,
            $previous
        );
    }

    public static function invalidValues(array $invalidValues, int $code = 0, ?\Throwable $previous = null) : self
    {
        return new static(
            sprintf('The following values are invalid: "%s"', implode('", "', $invalidValues)),
            $code,
            $previous
        );
    }
}