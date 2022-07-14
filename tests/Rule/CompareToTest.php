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
                    'compareAttribute' => null,
                    'message' => [
                        'message' => 'Value must be equal to "{compareValueOrAttribute}".',
                        'parameters' => [
                            'compareValue' => 1,
                            'compareAttribute' => null,
                            'compareValueOrAttribute' => 1,
                        ],
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
                    'compareAttribute' => null,
                    'message' => [
                        'message' => 'Value must be equal to "{compareValueOrAttribute}".',
                        'parameters' => [
                            'compareValue' => 1,
                            'compareAttribute' => null,
                            'compareValueOrAttribute' => 1,
                        ],
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
                    'compareAttribute' => null,
                    'message' => [
                        'message' => 'Value must be greater than or equal to "{compareValueOrAttribute}".',
                        'parameters' => [
                            'compareValue' => 1,
                            'compareAttribute' => null,
                            'compareValueOrAttribute' => 1,
                        ],
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
                    'compareAttribute' => null,
                    'message' => [
                        'message' => 'Value must be equal to "{compareValueOrAttribute}".',
                        'parameters' => [
                            'compareValue' => 'YES',
                            'compareAttribute' => null,
                            'compareValueOrAttribute' => 'YES',
                        ],
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
                    'compareAttribute' => null,
                    'message' => [
                        'message' => 'Value must be equal to "{compareValueOrAttribute}".',
                        'parameters' => [
                            'compareValue' => 'YES',
                            'compareAttribute' => null,
                            'compareValueOrAttribute' => 'YES',
                        ],
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
                    'compareAttribute' => null,
                    'message' => [
                        'message' => 'Value must not be equal to "{compareValueOrAttribute}".',
                        'parameters' => [
                            'compareValue' => 'YES',
                            'compareAttribute' => null,
                            'compareValueOrAttribute' => 'YES',
                        ],
                    ],
                    'type' => 'string',
                    'operator' => '!==',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new CompareTo('YES', message: 'Custom message for {compareValueOrAttribute}'),
                [
                    'compareValue' => 'YES',
                    'compareAttribute' => null,
                    'message' => [
                        'message' => 'Custom message for {compareValueOrAttribute}',
                        'parameters' => [
                            'compareValue' => 'YES',
                            'compareAttribute' => null,
                            'compareValueOrAttribute' => 'YES',
                        ],
                    ],
                    'type' => 'string',
                    'operator' => '==',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new CompareTo(null, 'test'),
                [
                    'compareValue' => null,
                    'compareAttribute' => 'test',
                    'message' => [
                        'message' => 'Value must be equal to "{compareValueOrAttribute}".',
                        'parameters' => [
                            'compareValue' => null,
                            'compareAttribute' => 'test',
                            'compareValueOrAttribute' => 'test',
                        ],
                    ],
                    'type' => 'string',
                    'operator' => '==',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new CompareTo(null, 'test', message: 'Custom message for {compareValueOrAttribute}'),
                [
                    'compareValue' => null,
                    'compareAttribute' => 'test',
                    'message' => [
                        'message' => 'Custom message for {compareValueOrAttribute}',
                        'parameters' => [
                            'compareValue' => null,
                            'compareAttribute' => 'test',
                            'compareValueOrAttribute' => 'test',
                        ],
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
