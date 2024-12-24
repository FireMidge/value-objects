<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\Helper\CanBeConvertedToStringArray;
use FireMidge\ValueObject\IsClassCollectionType;

/**
 * @extends IsClassCollectionType<SimpleIntType>
 */
class DefaultClassCollectionType
{
    use IsClassCollectionType;
    use CanBeConvertedToStringArray;

    protected static function className() : string
    {
        return SimpleIntType::class;
    }
}