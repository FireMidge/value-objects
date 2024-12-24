<?php
declare(strict_types=1);

namespace FireMidge\ValueObject\Exception;

use ValueError;
use DateTimeZone;

class ConversionError extends ValueError
{
    use RendersValue;

    private array $reasons = [];

    /**
     * @param string|null|string[] $message An additional, optional reason. Can be provided as a singular
     *                                      reason or multiple in array format.
     */
    public static function couldNotConvert(
        mixed $value,
        string $targetType,
        string|array|null $message = null
    ) : static
    {
        $self = new static(sprintf(
            'Could not convert value %s to %s.%s',
            static::renderValue($value),
            $targetType,
            $message === null ? '' : ' ' . $message
        ));
        $self->reasons = $message === null ? [] : (is_string($message) ? [$message] : $message);
        return $self;
    }

    /**
     * @param string|null|string[] $message An additional, optional reason. Can be provided as a singular
     *                                      reason or multiple in array format.
     */
    public static function couldNotParseDateString(
        string $value,
        string $format,
        ?DateTimeZone $timeZone,
        string|array|null $message = null
    ) : static
    {
        $self = new static(sprintf(
            'Could not convert date string "%s" to a date (from format "%s" and time zone %s). Reason(s): %s',
            $value,
            $format,
            $timeZone === null ? 'NULL' : $timeZone->getName(),
            implode('; ', $message)
        ));
        $self->reasons = $message === null ? [] : (is_string($message) ? [$message] : $message);
        return $self;
    }

    public function hasReason() : bool
    {
        return ! empty($this->reasons);
    }

    public function reason() : string
    {
        return implode('; ', $this->reasons);
    }

    public function reasons() : array
    {
        return $this->reasons;
    }
}