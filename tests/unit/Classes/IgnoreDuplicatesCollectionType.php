<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\IsCollectionType;

class IgnoreDuplicatesCollectionType
{
    use IsCollectionType;

    protected static function ignoreDuplicateValues() : bool
    {
        return true;
    }
}