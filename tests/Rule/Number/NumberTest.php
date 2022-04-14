<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Number;

use Yiisoft\Validator\Rule\Number\Number;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\Tests\Rule\AbstractRuleTest;

final class NumberTest extends AbstractRuleTest
{
    public function optionsDataProvider(): array
    {
        return [
            [
                new Number(),
                [
                    'asInteger' => false,
                    'min' => null,
                    'max' => null,
                    'notANumberMessage' => [
                        'message' => 'Value must be a number.',
                    ],
                    'tooSmallMessage' => [
                        'message' => 'Value must be no less than {min}.',
                        'parameters' => ['min' => null],
                    ],
                    'tooBigMessage' => [
                        'message' => 'Value must be no greater than {max}.',
                        'parameters' => ['max' => null],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Number(min: 1),
                [
                    'asInteger' => false,
                    'min' => 1,
                    'max' => null,
                    'notANumberMessage' => [
                        'message' => 'Value must be a number.',
                    ],
                    'tooSmallMessage' => [
                        'message' => 'Value must be no less than {min}.',
                        'parameters' => ['min' => 1],
                    ],
                    'tooBigMessage' => [
                        'message' => 'Value must be no greater than {max}.',
                        'parameters' => ['max' => null],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Number(max: 1),
                [
                    'asInteger' => false,
                    'min' => null,
                    'max' => 1,
                    'notANumberMessage' => [
                        'message' => 'Value must be a number.',
                    ],
                    'tooSmallMessage' => [
                        'message' => 'Value must be no less than {min}.',
                        'parameters' => ['min' => null],
                    ],
                    'tooBigMessage' => [
                        'message' => 'Value must be no greater than {max}.',
                        'parameters' => ['max' => 1],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Number(min: 2, max: 10),
                [
                    'asInteger' => false,
                    'min' => 2,
                    'max' => 10,
                    'notANumberMessage' => [
                        'message' => 'Value must be a number.',
                    ],
                    'tooSmallMessage' => [
                        'message' => 'Value must be no less than {min}.',
                        'parameters' => ['min' => 2],
                    ],
                    'tooBigMessage' => [
                        'message' => 'Value must be no greater than {max}.',
                        'parameters' => ['max' => 10],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Number(asInteger: true),
                [
                    'asInteger' => true,
                    'min' => null,
                    'max' => null,
                    'notANumberMessage' => [
                        'message' => 'Value must be an integer.',
                    ],
                    'tooSmallMessage' => [
                        'message' => 'Value must be no less than {min}.',
                        'parameters' => ['min' => null],
                    ],
                    'tooBigMessage' => [
                        'message' => 'Value must be no greater than {max}.',
                        'parameters' => ['max' => null],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    protected function getRule(): RuleInterface
    {
        return new Number();
    }
}
