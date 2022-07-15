<?php
declare(strict_types=1);

namespace FireMidge\ValueObject\Exception;

use Throwable;

/**
 * Thrown when a duplicate value is added where duplicates are disallowed.
 */
class DuplicateValue extends InvalidValue
{
    public static function containsDuplicates(
        array $arrayWithDuplicates,
        ?string $message = null,
        int $code = 0,
        ?Throwable  $previous = null
    ) : static
    {
        return new static(
            sprintf(
                'Values contain duplicates. Only unique values allowed. Values passed: %s.%s',
                implode(', ', array_map(function($v) { return static::renderValue($v); }, $arrayWithDuplicates)),
                $message === null ? '' : sprintf(' (%s)', $message)
            ),
            $code,
            $previous
        );
    }

    public static function duplicateValue(
        $duplicateValue,
        array $fullArray,
        ?string $message = null,
        int $code = 0,
        ?Throwable  $previous = null
    ) : static
    {
        return new static(
            sprintf(
                'Value %s cannot be used as it already exists within array. Existing values: %s.%s',
                static::renderValue($duplicateValue),
                implode(', ', array_map(function($v) { return static::renderValue($v); }, $fullArray)),
                $message === null ? '' : sprintf(' (%s)', $message)
            ),
            $code,
            $previous
        );
    }
}