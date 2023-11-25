<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Classes;

use FireMidge\ValueObject\Helper\CanExtractValueOfType;
use FireMidge\ValueObject\IsIntType;

class SimpleIntType
{
    use IsIntType;

    /**
     * Imported separately (without this, there are more false positives in the mutation test result).
     */
    use CanExtractValueOfType;
}