<?php
declare(strict_types=1);

namespace FireMidge\ValueObject\Generic;

use FireMidge\ValueObject\IsFloatType;
use JsonSerializable;

class Percentage implements JsonSerializable
{
    use IsFloatType;

    public function toInt() : int
    {
        return (int) round($this->value);
    }

    public function toString(int $decimalPlaces = 0) : string
    {
        return round($this->value, $decimalPlaces) . '%';
    }

    protected static function minValidValue() : ?float
    {
        return 0;
    }

    protected static function maxValidValue() : ?float
    {
        return 100;
    }
}