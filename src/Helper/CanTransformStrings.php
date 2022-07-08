<?php
declare(strict_types=1);

namespace FireMidge\ValueObject\Helper;

trait CanTransformStrings
{
    /**
     * A convenience method you can call inside transform().
     */
    private function trimAndLowerCase(string $value) : string
    {
        return mb_strtolower(trim($value));
    }

    /**
     * A convenience method you can call inside transform().
     */
    private function trimAndUpperCase(string $value) : string
    {
        return mb_strtoupper(trim($value));
    }

    /**
     * A convenience method you can call inside transform().
     */
    private function trimAndCapitalise(string $value) : string
    {
        return ucfirst(mb_strtolower(trim($value)));
    }
}