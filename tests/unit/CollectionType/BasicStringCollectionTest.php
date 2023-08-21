<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\CollectionType;

use FireMidge\Tests\ValueObject\Unit\Classes\BasicStringCollectionType;
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

    public function findValueProvider() : array
    {
        return [
            [ fn($v) => $v === 'HELLO', null, null ],
            [ fn($v) => $v === 'hellO', 'hellO', 5 ],
            [ fn($v) => $v === 'hellö', 'hellö', 4 ],
            [ fn($v) => str_starts_with($v, '1'), '1', 0 ],
            [ fn($v) => str_starts_with($v, '1h'), '1hello', 2 ],
            [ fn($v, $k) => $k === 3, 'hello', 3 ],
        ];
    }

    /**
     * @dataProvider findValueProvider
     */
    public function testFindValue(callable $callback, mixed $expectedValue, ?int $_) : void
    {
        $instance = BasicStringCollectionType::fromArray([
            '1',
            '1Hello',
            '1hello',
            'hello',
            'hellö',
            'hellO',
        ]);

        $this->assertSame($expectedValue, $instance->find($callback));
    }

    /**
     * @dataProvider findValueProvider
     */
    public function testFindIndex(callable $callback, mixed $_, ?int $expectedIndex) : void
    {
        $instance = BasicStringCollectionType::fromArray([
            '1',
            '1Hello',
            '1hello',
            'hello',
            'hellö',
            'hellO',
        ]);

        $this->assertSame($expectedIndex, $instance->findIndex($callback));
    }
}