<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\CollectionType;

use FireMidge\Tests\ValueObject\Unit\Classes\BasicStringCollectionType;
use FireMidge\ValueObject\IsCollectionType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\BasicStringCollectionType
 */
class BasicStringCollectionTest extends TestCase
{
    public function testFromArrayDuplicateValuesAllowed() : void
    {
        $input = [
            'hello',
            'hello',
        ];
        $instance = BasicStringCollectionType::fromArray($input);

        $this->assertSame($input, $instance->toArray());
    }

    public function testWithValueAllowsDuplicateValues() : void
    {
        $instance = BasicStringCollectionType::fromArray([
            'hello',
            'world',
        ]);
        $instance2 = $instance->withValue('hello');

        $this->assertSame([
            'hello',
            'world',
            'hello',
        ], $instance2->toArray(), 'Expected new instance elements to match');
        $this->assertSame([
            'hello',
            'world',
        ], $instance->toArray(), 'Expected original instance to have remained unchanged');
    }
}