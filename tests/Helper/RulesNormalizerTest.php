<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Helper;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Helper\RulesNormalizer;

final class RulesNormalizerTest extends TestCase
{
    public function dataNormalize(): array
    {
        return [
            'null' => [[], null, 0, null],
        ];
    }

    /**
     * @dataProvider dataNormalize
     */
    public function testNormalizeWithArrayResult(
        array $expected,
        iterable|object|string|null $rules,
        int $propertyVisibility,
        mixed $data
    ): void {
        $result = RulesNormalizer::normalize($rules, $propertyVisibility, $data);

        $this->assertSame($expected, $result);
    }
}
