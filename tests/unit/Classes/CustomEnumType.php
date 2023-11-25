<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

enum CustomEnumType
{
    public static function all(int $division) : array
    {
        return match ($division) {
            1       => [100, 101, 102, 103],
            2       => [202, 203, 204, 205],
            default => [
                1 => [100, 101, 102, 103],
                2 => [202, 203, 204, 205],
            ],
        };
    }

}
