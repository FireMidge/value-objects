<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\Helper\CanTransformStrings;
use FireMidge\ValueObject\IsStringArrayEnumType;

/**
 * May be useful for a FieldList - the "fields" query parameter of a RESTful API,
 * although in a fields list you might want to also use the validation to make
 * sure you have unique values - or don't care about duplicates.
 */
class StringArrayEnumType
{
    use IsStringArrayEnumType;
    use CanTransformStrings {
        // Aliasing the methods to be able to make them public.
        // This is ONLY to test them separately - otherwise this wouldn't make any sense,
        // especially not as non-static methods...
        CanTransformStrings::trimAndUpperCase as _trimAndUpperCase;
        CanTransformStrings::trimAndLowerCase as _trimAndLowerCase;
        CanTransformStrings::trimAndCapitalise as _trimAndCapitalise;
    }

    public function trimAndUpperCase(string $value) : string
    {
        return $this->_trimAndUpperCase($value);
    }

    public function trimAndLowerCase(string $value) : string
    {
        return $this->_trimAndLowerCase($value);
    }

    public function trimAndCapitalise(string $value) : string
    {
        return $this->_trimAndCapitalise($value);
    }

    protected static function all() : array
    {
        return [
            'name',
            'email',
            'status',
        ];
    }
}