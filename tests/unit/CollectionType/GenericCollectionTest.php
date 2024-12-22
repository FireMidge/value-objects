<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\CollectionType;

use FireMidge\ValueObject\Generic\AnyCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(AnyCollection::class)]
class GenericCollectionTest extends TestCase
{
    public static function successfulMergeProvider() : array
    {
        return [
            [ AnyCollection::fromArray([4, 5, 6]) ],
            [ [4, 5, 6] ],
        ];
    }

    #[DataProvider('successfulMergeProvider')]
    public function testMergeSuccessful(array|object $coll2) : void
    {
        $coll1 = AnyCollection::fromArray([1, 2, 3]);

        $coll1->merge($coll2);
        $this->assertEquals([1, 2, 3, 4, 5, 6], $coll1->toArray());

        if (is_object($coll2) && method_exists($coll2, 'toArray')) {
            $this->assertEquals(
                [4, 5, 6],
                $coll2->toArray(),
                'Expected the other collection to be unchanged'
            );
        }
    }

    #[DataProvider('successfulMergeProvider')]
    public function testWithMergedSuccessful(array|object $coll2) : void
    {
        $coll1 = AnyCollection::fromArray([1, 2, 3]);

        $coll3 = $coll1->withMerged($coll2);
        $this->assertEquals([1, 2, 3, 4, 5, 6], $coll3->toArray());
        $this->assertEquals(
            [1, 2, 3],
            $coll1->toArray(),
            'Source collection should not have been modified'
        );

        if (is_object($coll2) && method_exists($coll2, 'toArray')) {
            $this->assertEquals(
                [4, 5, 6],
                $coll2->toArray(),
                'Expected the other collection to be unchanged'
            );
        }
    }

    public function testPointers() : void
    {
        $coll = AnyCollection::fromArray(['a', 'b', 'c', 'd', 'e', 'f']);

        $this->assertTrue($coll->valid());
        $this->assertSame(0, $coll->key());
        $this->assertSame('a', $coll->current());

        $coll->next();
        $coll->next();
        $coll->next();

        $this->assertSame(3, $coll->key());
        $this->assertSame('d', $coll->current());

        $this->assertTrue($coll->valid());

        $coll->next();
        $coll->next();

        $this->assertSame(5, $coll->key());
        $this->assertSame('f', $coll->current());
        $this->assertTrue($coll->valid(), 'Expected true as we are still on the last element');

        $coll->next();
        $this->assertFalse($coll->valid(), 'Expected false as we are now out of bounds');
        $this->assertNull($coll->current());
        $this->assertNull($coll->key());
        $this->assertFalse($coll->valid(), 'Expected false as we are still out of bounds');

        $this->assertSame('a', $coll->first()); // Moves the pointer
        $this->assertSame('f', $coll->last()); // Moves the pointer

        $coll->rewind();
        $coll->next();

        $this->assertSame(1, $coll->key());
        $this->assertSame('b', $coll->current());
    }

    public function testPointersOnEmptyArray() : void
    {
        $coll = AnyCollection::empty();

        $this->assertFalse($coll->valid());
        $this->assertNull($coll->first());
        $this->assertNull($coll->last());
        $this->assertNull($coll->current());
        $coll->rewind();
        $this->assertNull($coll->current());
        $this->assertFalse($coll->valid());
    }

    public function testShuffle() : void
    {
        $coll = AnyCollection::fromArray(['a', 'b', 'c', 'd', 'e', 'f']);
        $this->assertEquals(['a', 'b', 'c', 'd', 'e', 'f'], $coll->toArray());

        $coll->shuffle();
        $this->assertNotEquals(['a', 'b', 'c', 'd', 'e', 'f'], $coll->toArray());
        $this->assertCount(6, $coll->toArray());

        $reSortedArray = $coll->toArray();
        sort($reSortedArray);
        $this->assertEquals(['a', 'b', 'c', 'd', 'e', 'f'], $reSortedArray);
    }

    public function testPop() : void
    {
        $coll = AnyCollection::fromArray(['a', 'b', 'c']);

        $popped = $coll->pop();
        $this->assertSame('c', $popped);
        $this->assertSame(['a', 'b'], $coll->toArray(), 'Expected pop() to modify the current instance');
    }

    public function testPopWithEmpty() : void
    {
        $coll = AnyCollection::empty();
        $popped = $coll->pop();

        $this->assertNull($popped);
        $this->assertCount(0, $coll->toArray());
    }

    public function testPopMultiple() : void
    {
        $coll = AnyCollection::fromArray(['a', 'b', 'c', 'd', 'e', 'f']);

        $popped = $coll->popMultiple(4);
        $this->assertInstanceOf(AnyCollection::class, $popped);
        $this->assertSame(
            ['f', 'e', 'd', 'c'],
            $popped->toArray(),
            'Expect popMultiple to pop each value individually, resulting in a reverse order'
        );
        $this->assertSame(['a', 'b'], $coll->toArray(), 'Expected popMultiple() to modify the current instance');
    }

    public function testPopMultipleWithEmptyDoesNotError() : void
    {
        $coll = AnyCollection::empty();

        $popped = $coll->popMultiple(3);
        $this->assertInstanceOf(AnyCollection::class, $popped);
        $this->assertSame(0, $popped->count());
    }

    public function testPopMultipleWithMoreElementsThanInArray() : void
    {
        $coll = AnyCollection::fromArray(['a', 'b', 'c']);

        $popped = $coll->popMultiple(4);
        $this->assertInstanceOf(AnyCollection::class, $popped);
        $this->assertCount(3, $popped->toArray());
        $this->assertCount(0, $coll->toArray());
    }

    public function testSplit() : void
    {
        $coll = AnyCollection::fromArray(['a', 'b', 'c', 'd', 'e', 'f']);

        $split = $coll->split(4);
        $this->assertInstanceOf(AnyCollection::class, $split);
        $this->assertSame(
            ['e', 'f'],
            $split->toArray(),
            'Expected split() to keep the original order'
        );
        $this->assertSame(
            ['a', 'b', 'c', 'd'],
            $coll->toArray(),
            'Expected split() to modify the current instance'
        );
    }

    public function testSplitWithZero() : void
    {
        $coll = AnyCollection::fromArray(['a', 'b', 'c', 'd', 'e', 'f']);

        $split = $coll->split(0);
        $this->assertInstanceOf(AnyCollection::class, $split);
        $this->assertSame(
            ['a', 'b', 'c', 'd', 'e', 'f'],
            $split->toArray(),
            'Expected split() with 0 values to keep to remove all'
        );
        $this->assertSame(
            [],
            $coll->toArray(),
            'Expected split() to modify the current instance'
        );
    }

    public function testSplitWithNegativeNumber() : void
    {
        $coll = AnyCollection::fromArray(['a', 'b', 'c', 'd', 'e', 'f']);

        $split = $coll->split(-4);
        $this->assertInstanceOf(AnyCollection::class, $split);
        $this->assertSame(
            ['c', 'd', 'e', 'f'],
            $split->toArray(),
            'Expected split() with $numberOfValuesToKeep of -4 to keep everything but the last 4 values'
        );
        $this->assertSame(
            ['a', 'b'],
            $coll->toArray(),
            'Expected split() to modify the current instance'
        );
    }

    public function testSplitWithHigherNumberThanInArray() : void
    {
        $coll = AnyCollection::fromArray(['a', 'b', 'c']);

        $split = $coll->split(5);
        $this->assertInstanceOf(AnyCollection::class, $split);
        $this->assertSame(
            [],
            $split->toArray(),
            'Expected for 0 values to be split off from the original, as we said we want to keep 5 and we only have 3 in total'
        );
        $this->assertSame(
            ['a', 'b', 'c'],
            $coll->toArray(),
            'Expected all values to remain in the original'
        );
    }

    public function testWithReversedOrder() : void
    {
        $coll = AnyCollection::fromArray(['a', 'b', 'c', 'd', 'e', 'f']);

        $this->assertEquals(
            ['f', 'e', 'd', 'c', 'b', 'a'],
            $coll->withReversedOrder()->toArray()
        );

        $this->assertEquals(
            ['a', 'b', 'c', 'd', 'e', 'f'],
            $coll->toArray(),
            'Expected the original not to have been modified'
        );
    }

    public function testWithReversedOrderOnEmptyArrayDoesNotError() : void
    {
        $coll = AnyCollection::empty();

        $this->assertEquals(
            [],
            $coll->withReversedOrder()->toArray()
        );
    }
}