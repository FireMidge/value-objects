<?php
declare(strict_types=1);

namespace FireMidge\ValueObject\Exception;

use DateTimeZone;
use ValueError;

class ConversionError extends ValueError
{
    use RendersValue;

    public static function couldNotConvert(mixed $value, string $targetType, ?string $message = null) : static
    {
        return new static(sprintf(
            'Could not convert value %s to %s.%s',
            static::renderValue($value),
            $targetType,
            $message === null ? '' : ' ' . $message
        ));
    }

    public static function couldNotParseDateString(
        string $value,
        string $format,
        ?DateTimeZone $timeZone,
        ?string $message = null
    ) : static
    {
        return new static(sprintf(
            'Could not convert date string "%s" to a date (from format "%s" and time zone %s). Reason(s): %s',
            $value,
            $format,
            $timeZone === null ? 'NULL' : $timeZone->getName(),
            $message
        ));
    }
}