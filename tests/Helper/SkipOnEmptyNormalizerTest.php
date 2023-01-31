<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Helper;

use Closure;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\EmptyCondition\NeverEmpty;
use Yiisoft\Validator\EmptyCondition\WhenEmpty;
use Yiisoft\Validator\Helper\SkipOnEmptyNormalizer;

final class SkipOnEmptyNormalizerTest extends TestCase
{
    public function normalizeData(): array
    {
        return [
            [null, NeverEmpty::class],
            [false, NeverEmpty::class],
            [true, WhenEmpty::class],
            [static fn (mixed $value, bool $isAttributeMissing): bool => true, Closure::class],
        ];
    }

    /**
     * @dataProvider normalizeData
     */
    public function testNormalize(mixed $skipOnEmpty, string $expectedClassName): void
    {
        $this->assertInstanceOf($expectedClassName, SkipOnEmptyNormalizer::normalize($skipOnEmpty));
    }

    public function testWrongType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$skipOnEmpty must be a null, a boolean or a callable');
        SkipOnEmptyNormalizer::normalize(1);
    }
}
