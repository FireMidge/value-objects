<?php
declare(strict_types=1);

namespace FireMidge\ValueObject\Generic;

use FireMidge\ValueObject\IsCollectionType;
use Iterator;

class AnyCollection implements Iterator
{
    use IsCollectionType;
}