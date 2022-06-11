<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Rule\CompareTo;
use Yiisoft\Validator\ParametrizedRuleInterface;

final class CompareToTest extends AbstractRuleTest
{
    public function optionsDataProvider(): array
    {
        return [
            [
                new CompareTo(1),
                [
                    'compareValue' => 1,
                    'message' => [
                        'message' => 'Value must be equal to "{compareValue}".',
                        'parameters' => ['compareValue' => 1],
                    ],
                    'type' => 'string',
                    'operator' => '==',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new CompareTo(1, type: CompareTo::TYPE_NUMBER),
                [
                    'compareValue' => 1,
                    'message' => [
                        'message' => 'Value must be equal to "{compareValue}".',
                        'parameters' => ['compareValue' => 1],
                    ],
                    'type' => 'number',
                    'operator' => '==',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new CompareTo(1, type: CompareTo::TYPE_NUMBER, operator: '>='),
                [
                    'compareValue' => 1,
                    'message' => [
                        'message' => 'Value must be greater than or equal to "{compareValue}".',
                        'parameters' => ['compareValue' => 1],
                    ],
                    'type' => 'number',
                    'operator' => '>=',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new CompareTo('YES'),
                [
                    'compareValue' => 'YES',
                    'message' => [
                        'message' => 'Value must be equal to "{compareValue}".',
                        'parameters' => ['compareValue' => 'YES'],
                    ],
                    'type' => 'string',
                    'operator' => '==',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new CompareTo('YES', skipOnEmpty: true),
                [
                    'compareValue' => 'YES',
                    'message' => [
                        'message' => 'Value must be equal to "{compareValue}".',
                        'parameters' => ['compareValue' => 'YES'],
                    ],
                    'type' => 'string',
                    'operator' => '==',
                    'skipOnEmpty' => true,
                    'skipOnError' => false,
                ],
            ],
            [
                new CompareTo('YES', operator: '!=='),
                [
                    'compareValue' => 'YES',
                    'message' => [
                        'message' => 'Value must not be equal to "{compareValue}".',
                        'parameters' => ['compareValue' => 'YES'],
                    ],
                    'type' => 'string',
                    'operator' => '!==',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new CompareTo('YES', message: 'Custom message for {compareValue}'),
                [
                    'compareValue' => 'YES',
                    'message' => [
                        'message' => 'Custom message for {compareValue}',
                        'parameters' => ['compareValue' => 'YES'],
                    ],
                    'type' => 'string',
                    'operator' => '==',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    protected function getRule(): ParametrizedRuleInterface
    {
        return new CompareTo(1);
    }
}
