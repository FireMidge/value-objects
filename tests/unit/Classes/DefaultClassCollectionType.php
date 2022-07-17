<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\Helper\CanBeConvertedToStringArray;
use FireMidge\ValueObject\IsClassCollectionType;

class DefaultClassCollectionType
{
    use IsClassCollectionType;
    use CanBeConvertedToStringArray;

    protected function className() : string
    {
        return SimpleIntType::class;
    }
}