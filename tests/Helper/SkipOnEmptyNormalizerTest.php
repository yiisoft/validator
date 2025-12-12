<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Helper;

use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\EmptyCondition\NeverEmpty;
use Yiisoft\Validator\EmptyCondition\WhenEmpty;
use Yiisoft\Validator\Helper\SkipOnEmptyNormalizer;

final class SkipOnEmptyNormalizerTest extends TestCase
{
    public static function normalizeData(): array
    {
        return [
            [null, NeverEmpty::class],
            [false, NeverEmpty::class],
            [true, WhenEmpty::class],
            [static fn(mixed $value, bool $isPropertyMissing): bool => true, Closure::class],
        ];
    }

    #[DataProvider('normalizeData')]
    public function testNormalize(bool|callable|null $skipOnEmpty, string $expectedClassName): void
    {
        $this->assertInstanceOf($expectedClassName, SkipOnEmptyNormalizer::normalize($skipOnEmpty));
    }
}
