<?php
declare(strict_types=1);

namespace FireMidge\ValueObject\Generic;

use FireMidge\ValueObject\IsStringType;
use JsonSerializable;

class AnyString implements JsonSerializable
{
    use IsStringType;
}