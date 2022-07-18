<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\CollectionType;

use FireMidge\Tests\ValueObject\Unit\Classes\IgnoreDuplicatesCollectionType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\IgnoreDuplicatesCollectionType
 */
class IgnoreDuplicatesCollectionTest extends TestCase
{
    public function testDefaultValidation() : void
    {
        $instance = IgnoreDuplicatesCollectionType::fromArray([true]);
        $this->assertSame([true], $instance->toArray());
    }

    public function testAddingDuplicateValueIgnoresIt() : void
    {
        $instance = IgnoreDuplicatesCollectionType::fromArray([1]);
        $instance2 = $instance->withValue(1);

        $this->assertSame([1], $instance2->toArray(), 'Expected value to only exist once in instance2');
        $this->assertSame([1], $instance->toArray(), 'Expected original instance to not have changed');
    }

    public function testFromArrayWithDuplicateValues() : void
    {
        $instance = IgnoreDuplicatesCollectionType::fromArray([1, 1]);

        $this->assertSame([1], $instance->toArray(), 'Expected value to only exist once');
    }
}