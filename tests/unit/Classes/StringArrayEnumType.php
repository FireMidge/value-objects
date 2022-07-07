<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\IsStringArrayEnumType;

/**
 * May be useful for a FieldList - the "fields" query parameter of a RESTful API,
 * although in a fields list you might want to also use the validation to make
 * sure you have unique values - or don't care about duplicates.
 */
class StringArrayEnumType
{
    use IsStringArrayEnumType;

    protected static function all() : array
    {
        return [
            'name',
            'email',
            'status',
        ];
    }
}