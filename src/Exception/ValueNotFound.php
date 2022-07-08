<?php
declare(strict_types=1);

namespace FireMidge\ValueObject\Exception;

use RuntimeException;

class ValueNotFound extends RuntimeException
{
    public static function inArray($value, array $availableValues) : self
    {
        return new static(sprintf(
            'Value "%s" was not found. Available values: "%s"',
            $value,
            implode('", "', $availableValues)
        ));
    }
}