<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Nested;

use Yiisoft\Validator\Rule\Nested\Nested;
use Yiisoft\Validator\Rule\Number\Number;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\Tests\Rule\AbstractRuleTest;

/**
 * @group t
 */
final class NestedTest extends AbstractRuleTest
{
    public function optionsDataProvider(): array
    {
        return [
            [
                new Nested([new Number()]),
                [
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
            ],
            [
                new Nested(['user.age' => new Number()]),
                [
                    'user.age' => [
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
            ],
        ];
    }

    protected function getRule(): RuleInterface
    {
        return new Nested([]);
    }
}
