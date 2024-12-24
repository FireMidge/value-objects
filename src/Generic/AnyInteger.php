<?php
declare(strict_types=1);

namespace FireMidge\ValueObject\Generic;

use FireMidge\ValueObject\IsIntType;
use JsonSerializable;

class AnyInteger implements JsonSerializable
{
    use IsIntType;
}