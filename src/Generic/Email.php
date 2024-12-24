<?php
declare(strict_types=1);

namespace FireMidge\ValueObject\Generic;

use FireMidge\ValueObject\IsEmailType;
use JsonSerializable;

class Email implements JsonSerializable
{
    use IsEmailType;
}