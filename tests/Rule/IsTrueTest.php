<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Rule\IsTrue;
use Yiisoft\Validator\SerializableRuleInterface;

final class IsTrueTest extends AbstractRuleTest
{
    public function testGetName(): void
    {
        $rule = new IsTrue();
        $this->assertSame('isTrue', $rule->getName());
    }

    public function optionsDataProvider(): array
    {
        return [
            [
                new IsTrue(),
                [
                    'trueValue' => '1',
                    'strict' => false,
                    'message' => [
                        'message' => 'The value must be "{true}".',
                        'parameters' => [
                            'true' => '1',
                        ],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new IsTrue(trueValue: true, strict: true),
                [
                    'trueValue' => true,
                    'strict' => true,
                    'message' => [
                        'message' => 'The value must be "{true}".',
                        'parameters' => [
                            'true' => 'true',
                        ],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new IsTrue(
                    trueValue: 'YES',
                    strict: true,
                    message: 'Custom message.',
                    skipOnEmpty: true,
                    skipOnError: true
                ),
                [
                    'trueValue' => 'YES',
                    'strict' => true,
                    'message' => [
                        'message' => 'Custom message.',
                        'parameters' => [
                            'true' => 'YES',
                        ],
                    ],
                    'skipOnEmpty' => true,
                    'skipOnError' => true,
                ],
            ],
        ];
    }

    protected function getRule(): SerializableRuleInterface
    {
        return new IsTrue();
    }
}
