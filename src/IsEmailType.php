<?php
declare(strict_types=1);

namespace FireMidge\ValueObject;

use FireMidge\ValueObject\Exception\InvalidValue;

/**
 * A trait for value objects that consist of an e-mail address.
 */
trait IsEmailType
{
    use IsStringType;

    /**
     * @throws InvalidValue If $value does not have a valid e-mail address format.
     */
    protected function validate(string $value) : void
    {
        $this->validateEmailAddress($value);
    }

    protected function transform(string $value) : string
    {
        return $this->trimAndLowerCase($value);
    }
}