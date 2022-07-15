<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\IsStringArrayEnumType;

class StringArrayEnumUpperCaseType
{
    use IsStringArrayEnumType;

    protected static function all() : array
    {
        return [
            'Name',
            'Email',
            'Status',
        ];
    }
}