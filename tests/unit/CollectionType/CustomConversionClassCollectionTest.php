<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\CollectionType;

use FireMidge\Tests\ValueObject\Unit\Classes\ClassCollectionWithCustomConverterType;
use FireMidge\Tests\ValueObject\Unit\Classes\PrivateConstructorObject;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ClassCollectionWithCustomConverterType::class)]
class CustomConversionClassCollectionTest extends TestCase
{
    public function testCustomConversionSuccessful() : void
    {
        $expected = [
            PrivateConstructorObject::fromCustom('it'),
        ];

        $actual = ClassCollectionWithCustomConverterType::fromRawArray([
            'hello@services.it',
        ]);

        $this->assertEquals($expected, $actual->toArray());
    }
}