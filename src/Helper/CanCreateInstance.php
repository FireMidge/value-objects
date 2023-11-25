<?php
declare(strict_types=1);

namespace FireMidge\ValueObject\Helper;

use FireMidge\ValueObject\Exception\ConversionError;
use Throwable;

/**
 * A trait to turn values into instances of a specified target class.
 */
trait CanCreateInstance
{
    /**
     * Whether to allow converting a value to string before calling a static fromString() method with it
     * in order to instantiate the target class.
     *
     * Override to return a different value.
     */
    protected static function shouldAllowToStringConversion() : bool
    {
        return false;
    }

    private static function convertIntoInstance(mixed $value, string $className) : object
    {
        if (is_string($value) && method_exists($className, 'fromString')) {
            return $className::fromString($value);
        }

        if (is_array($value) && method_exists($className, 'fromArray')) {
            return $className::fromArray($value);
        }

        if (is_int($value) && method_exists($className, 'fromInt')) {
            return $className::fromInt($value);
        }

        if (is_bool($value) && method_exists($className, 'fromBool')) {
            return $className::fromBool($value);
        }

        if (is_float($value) && method_exists($className, 'fromFloat')) {
            return $className::fromFloat($value);
        }

        if (is_float($value) && method_exists($className, 'fromDouble')) {
            return $className::fromDouble($value);
        }

        if ((is_float($value) || is_int($value)) && method_exists($className, 'fromNumber')) {
            return $className::fromNumber($value);
        }

        try {
            return new $className($value);
        } catch (Throwable) {
            // Try others.
        }

        $error = ConversionError::couldNotConvert(
            $value,
            $className,
            sprintf('Override %s to customise conversion', __METHOD__)
        );

        if (! static::shouldAllowToStringConversion()
            || (
                ! method_exists($className, 'fromString')
                || (is_bool($value) || is_array($value) || is_object($value))
                // ^ We do not want to automatically convert to string from a boolean, as this will most
                // likely lead to unexpected behaviour.
            )
        ) {
            throw $error;
        }

        try {
            return $className::fromString((string) $value);
        } catch (Throwable) {
            throw $error;
        }
    }
}