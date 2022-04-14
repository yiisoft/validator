<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Each;

use Yiisoft\Validator\Rule\Each\Each;
use Yiisoft\Validator\Rule\Number\Number;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\Tests\Rule\AbstractRuleTest;

/**
 * @group t
 */
final class EachTest extends AbstractRuleTest
{
    public function optionsDataProvider(): array
    {
        return [
            [
                new Each([new Number(max: 13), new Number(max: 14)]),
                [
                    [
                        'number',
                        'asInteger' => false,
                        'min' => null,
                        'max' => 13,
                        'notANumberMessage' => [
                            'message' => 'Value must be a number.',
                        ],
                        'tooSmallMessage' => [
                            'message' => 'Value must be no less than {min}.',
                            'parameters' => ['min' => null],
                        ],
                        'tooBigMessage' => [
                            'message' => 'Value must be no greater than {max}.',
                            'parameters' => ['max' => 13],
                        ],
                        'skipOnEmpty' => false,
                        'skipOnError' => false,
                    ],
                    [
                        'number',
                        'asInteger' => false,
                        'min' => null,
                        'max' => 14,
                        'notANumberMessage' => [
                            'message' => 'Value must be a number.',
                        ],
                        'tooSmallMessage' => [
                            'message' => 'Value must be no less than {min}.',
                            'parameters' => ['min' => null],
                        ],
                        'tooBigMessage' => [
                            'message' => 'Value must be no greater than {max}.',
                            'parameters' => ['max' => 14],
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
        return new Each([]);
    }
}
