<?php
declare(strict_types=1);

namespace FireMidge\ValueObject\Generic;

use FireMidge\ValueObject\IsCollectionType;
use Iterator;
use JsonSerializable;

class AnyCollection implements Iterator, JsonSerializable
{
    use IsCollectionType;
}