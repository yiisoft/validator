<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Rule\InRange;
use Yiisoft\Validator\RuleInterface;

final class InRangeTest extends AbstractRuleTest
{
    public function optionsDataProvider(): array
    {
        return [
            [
                new InRange(range(1, 10)),
                [
                    'range' => range(1, 10),
                    'strict' => false,
                    'not' => false,
                    'message' => [
                        'message' => 'This value is invalid.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new InRange(range(1, 2), strict: true),
                [
                    'range' => [1, 2],
                    'strict' => true,
                    'not' => false,
                    'message' => [
                        'message' => 'This value is invalid.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new InRange(range(1, 2), not: true),
                [
                    'range' => [1, 2],
                    'strict' => false,
                    'not' => true,
                    'message' => [
                        'message' => 'This value is invalid.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    protected function getRule(): RuleInterface
    {
        return new InRange([]);
    }
}
