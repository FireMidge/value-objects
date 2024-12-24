<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\Helper\CanBeConvertedToStringArray;
use FireMidge\ValueObject\IsClassCollectionType;
use FireMidge\ValueObject\IsCollectionType;

/**
 * @extends IsCollectionType<SimpleStringType>
 */
class StringClassCollectionType
{
    use IsClassCollectionType;
    use CanBeConvertedToStringArray;

    protected static function className() : string
    {
        return SimpleStringType::class;
    }

    protected static function ignoreDuplicateValues() : bool
    {
        return true;
    }
}