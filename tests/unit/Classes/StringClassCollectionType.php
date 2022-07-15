<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\Helper\CanBeConvertedToStringArray;
use FireMidge\ValueObject\IsClassCollectionType;

class StringClassCollectionType
{
    use IsClassCollectionType;
    use CanBeConvertedToStringArray;

    protected function className() : string
    {
        return SimpleStringType::class;
    }
}