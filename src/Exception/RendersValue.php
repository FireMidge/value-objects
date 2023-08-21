<?php
declare(strict_types=1);

namespace FireMidge\ValueObject\Exception;

trait RendersValue
{
    protected static function renderValue(mixed $value) : string
    {
        if ($value === null) {
            return '{null}';
        }

        if (is_string($value)) {
            return sprintf('"%s"', $value);
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        if (is_array($value)) {
            return sprintf(
                'Array(%s)',
                implode(
                    ', ',
                    array_map(
                        fn($v) => static::renderValue($v),
                        $value
                    )
                )
            );
        }

        // TODO: Is this ever even invoked any more?
        if (! is_object($value)) {
            return sprintf('"%s"', $value);
        }

        if (method_exists( $value, '__toString' )) {
            return sprintf('"%s"', $value);
        }

        if (method_exists( $value, 'toString' )) {
            return sprintf('"%s"', $value->toString());
        }

        return sprintf('of type %s', get_class($value));
    }
}