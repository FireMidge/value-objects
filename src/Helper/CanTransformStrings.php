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
        $allLowerCase = (mb_strtolower(trim($value)));
        return mb_strtoupper(mb_substr($allLowerCase, 0, 1)) . mb_substr($allLowerCase, 1);
    }
}