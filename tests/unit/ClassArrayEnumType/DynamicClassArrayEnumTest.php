<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\ClassArrayEnumType;

use FireMidge\Tests\ValueObject\Unit\Classes\CustomEnumType;
use FireMidge\Tests\ValueObject\Unit\Classes\CustomEnumWithPrivateAllType;
use FireMidge\Tests\ValueObject\Unit\Classes\DynamicClassArrayEnumType;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\DynamicClassArrayEnumType
 */
class DynamicClassArrayEnumTest extends TestCase
{
    public function testAllMethodNotImplemented() : void
    {
        DynamicClassArrayEnumType::useClass(SimpleObject::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method FireMidge\Tests\ValueObject\Unit\Classes\SimpleObject::all is not implemented');

        DynamicClassArrayEnumType::withAll();
    }

    public function testAllMethodRequiresAParameter() : void
    {
        DynamicClassArrayEnumType::useClass(CustomEnumType::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method FireMidge\Tests\ValueObject\Unit\Classes\CustomEnumType::all is not callable. Make sure it has no required parameters and it returns an array. Too few arguments to function FireMidge\Tests\ValueObject\Unit\Classes\CustomEnumType::all(), 0 passed and exactly 1 expected');

        DynamicClassArrayEnumType::withAll();
    }

    public function testAllMethodIsProtected() : void
    {
        DynamicClassArrayEnumType::useClass(CustomEnumWithPrivateAllType::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method FireMidge\Tests\ValueObject\Unit\Classes\CustomEnumWithPrivateAllType::all is not implemented or is not callable. Make sure it is public.');

        DynamicClassArrayEnumType::withAll();
    }
}
