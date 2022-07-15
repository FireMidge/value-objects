<?php
declare(strict_types=1);

namespace FireMidge\ValueObject\Helper;

trait CanBeConvertedToStringArray
{
    /**
     * Converts every array element to a string and returns
     * them as an array of string values.
     */
    public function toStringArray() : array
    {
        return array_map(function ($v) {
            return (string) $v;
        }, $this->toArray());
    }

    public abstract function toArray() : array;
}