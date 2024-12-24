<?php
declare(strict_types=1);

namespace FireMidge\ValueObject\Generic;

use FireMidge\ValueObject\IsFloatType;
use JsonSerializable;

class AnyFloat implements JsonSerializable
{
    use IsFloatType;
}