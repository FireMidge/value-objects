<?php
declare(strict_types=1);

namespace FireMidge\Tests\ValueObject\Unit\Exception;

use FireMidge\Tests\ValueObject\Unit\Classes\ValueRenderer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Tests the RendersValue trait.
 *
 * @covers \FireMidge\Tests\ValueObject\Unit\Classes\ValueRenderer
 */
#[CoversClass(ValueRenderer::class)]
class ValueRendererTest extends TestCase
{
    public function testRendersMultiDimensionalStringArray() : void
    {
        $this->assertSame(
            'Array("Hello", Array("Moi", "World"))',
            ValueRenderer::renderValue(['Hello', ['Moi', 'World']])
        );
    }

    public function testRendersMultiDimensionalMixedArray() : void
    {
        $this->assertSame(
            'Array("Hello", Array("Moi", "World", false), 15, {null})',
            ValueRenderer::renderValue(['Hello', ['Moi', 'World', false], 15, null])
        );
    }

    public static function simpleScalarValueProvider() : array
    {
        return [
            [true, 'true'],
            [false, 'false'],
            ['hello World', '"hello World"'],
            ['äöüÄÖÜéýúáøØæÆ', '"äöüÄÖÜéýúáøØæÆ"'],
            [1005, '1005'],
            [-15.5876, '-15.5876'],
            [null, '{null}'],
        ];
    }

    #[DataProvider('simpleScalarValueProvider')]
    public function testRendersSimpleScalarValues(mixed $input, string $expected) : void
    {
        $this->assertSame(
            $expected,
            ValueRenderer::renderValue($input)
        );
    }
}