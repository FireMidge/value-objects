<?php
declare(strict_types=1);

namespace FireMidge\ValueObject\Exception;

use RuntimeException;

class ConversionError extends RuntimeException
{
    use RendersValue;

    public static function couldNotConvert(mixed $value, string $targetType, ?string $message = null) : static
    {
        return new static(sprintf(
            'Could not convert value %s to %s. %s',
            static::renderValue($value),
            $targetType,
            $message ?? ''
        ));
    }
}