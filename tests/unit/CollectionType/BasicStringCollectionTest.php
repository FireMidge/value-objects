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

    public function testCurrentNextAndPrevious() : void
    {
        $array = ['uno', 'due', 'tre', 'quattro', 'cinque', 'sei', 'sette', 'otto', 'nove', 'dieci'];

        $instance = BasicStringCollectionType::fromArray($array);

        $this->assertSame(
            'uno',
            $instance->current(),
            'Expected the first "current" element to be uno'
        );

        $instance->next();
        $this->assertSame(
            'due',
            $instance->current(),
            'Expected the next element to be due'
        );

        $instance->next(); // tre
        $instance->next(); // quattro
        $instance->next(); // cinque

        $this->assertSame(
            'cinque',
            $instance->current(),
            'Expected the 3rd "current" element to be cinque'
        );

        $instance->previous();

        $this->assertSame(
            'quattro',
            $instance->current(),
            'Expected the 4th "current" element to be quattro'
        );
    }

    public function testFirstAndLast() : void
    {
        $array = ['uno', 'due', 'tre', 'quattro', 'cinque', 'sei', 'sette', 'otto', 'nove', 'dieci'];

        $instance = BasicStringCollectionType::fromArray($array);

        $this->assertSame(
            'uno',
            $instance->first(),
            'Expected the first element to be uno'
        );

        $instance->next();
        $instance->next();

        $this->assertSame(
            'uno',
            $instance->first(),
            'Expected the first element to remain uno, regardless of number of next() calls.'
        );

        $this->assertSame(
            'dieci',
            $instance->last(),
            'Expected the last element to be dieci.'
        );
    }

    public function testRewindAndKey() : void
    {
        $array = ['uno', 'due', 'tre', 'quattro', 'cinque', 'sei', 'sette', 'otto', 'nove', 'dieci'];

        $instance = BasicStringCollectionType::fromArray($array);

        $this->assertSame(0, $instance->key(), 'Expected first key to be 0');

        $instance->next();
        $instance->next();

        $this->assertSame(
            'tre',
            $instance->current(),
            'Expected current element to return the 3rd element'
        );

        $this->assertSame(
            2,
            $instance->key(),
            'Expected current key to have moved with the pointer'
        );

        $instance->rewind();

        $this->assertSame(
            'uno',
            $instance->current(),
            'Expected the current element to be the first one again since we rewound the pointer.'
        );

        $this->assertSame(0, $instance->key(), 'Expected key to have been rewound as well');

        $this->assertTrue(
            $instance->valid(),
            'Expected true because the pointer is still within a valid range'
        );
    }

    public function testPointersOutsideOfRange() : void
    {
        $array = ['uno', 'due', 'tre', 'quattro', 'cinque', 'sei', 'sette', 'otto', 'nove', 'dieci'];

        $instance = BasicStringCollectionType::fromArray($array);

        $this->assertSame(
            'uno',
            $instance->first(),
            'Expected the first element to be uno'
        );

        $instance->previous();
        $this->assertNull($instance->current(), 'Expected element before first not to exist');

        $this->assertSame(
            'dieci',
            $instance->last(),
            'Expected the last element to be dieci.'
        );
        $this->assertTrue($instance->valid());

        $instance->next();
        $this->assertNull($instance->current(), 'Expected element after last not to exist');
        $this->assertFalse($instance->valid());
    }
}