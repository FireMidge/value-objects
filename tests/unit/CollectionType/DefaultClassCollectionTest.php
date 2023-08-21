<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\CollectionType;

use FireMidge\Tests\ValueObject\Unit\Classes\DefaultClassCollectionType;
use FireMidge\Tests\ValueObject\Unit\Classes\SimpleIntType;
use FireMidge\ValueObject\Exception\DuplicateValue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\DefaultClassCollectionType
 */
class DefaultClassCollectionTest extends TestCase
{
    public function testFromArrayThrowsOnDuplicateValuesProvided() : void
    {
        $this->expectException(DuplicateValue::class);
        $this->expectExceptionMessage('Values contain duplicates. Only unique values allowed. Values passed: "5", "5"');

        DefaultClassCollectionType::fromArray([
            SimpleIntType::fromInt(5),
            SimpleIntType::fromInt(5),
        ]);
    }

    public function testWithValueThrowsOnDuplicateValuesProvided() : void
    {
        $this->expectException(DuplicateValue::class);
        $this->expectExceptionMessage('Value "5" cannot be used as it already exists within array. Existing values: "5", "15"');

        $instance = DefaultClassCollectionType::fromArray([
            SimpleIntType::fromInt(5),
            SimpleIntType::fromInt(15),
        ]);

        $instance->withValue(SimpleIntType::fromInt(5));
    }

    public function testFromRawValuesFromInt() : void
    {
        $instance = DefaultClassCollectionType::fromRawArray([5, 15]);

        $this->assertEquals([
            SimpleIntType::fromInt(5),
            SimpleIntType::fromInt(15),
        ], $instance->toArray());
    }

    public function testFromRawValuesFromString() : void
    {
        $instance = DefaultClassCollectionType::fromRawArray(['5', '15']);

        $this->assertEquals([
            SimpleIntType::fromInt(5),
            SimpleIntType::fromInt(15),
        ], $instance->toArray());
    }
}