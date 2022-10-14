<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use stdClass;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\NumberHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SerializableRuleTestTrait;

final class NumberTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use SerializableRuleTestTrait;

    public function testGetName(): void
    {
        $rule = new Number();
        $this->assertSame('number', $rule->getName());
    }

    public function dataOptions(): array
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
                    'integerPattern' => '/^\s*[+-]?\d+\s*$/',
                    'numberPattern' => '/^\s*[-+]?\d*\.?\d+([eE][-+]?\d+)?\s*$/',
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
                    'integerPattern' => '/^\s*[+-]?\d+\s*$/',
                    'numberPattern' => '/^\s*[-+]?\d*\.?\d+([eE][-+]?\d+)?\s*$/',
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
                    'integerPattern' => '/^\s*[+-]?\d+\s*$/',
                    'numberPattern' => '/^\s*[-+]?\d*\.?\d+([eE][-+]?\d+)?\s*$/',
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
                    'integerPattern' => '/^\s*[+-]?\d+\s*$/',
                    'numberPattern' => '/^\s*[-+]?\d*\.?\d+([eE][-+]?\d+)?\s*$/',
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
                    'integerPattern' => '/^\s*[+-]?\d+\s*$/',
                    'numberPattern' => '/^\s*[-+]?\d*\.?\d+([eE][-+]?\d+)?\s*$/',
                ],
            ],
        ];
    }

    public function dataValidationPassed(): array
    {
        return [
            [20, [new Number()]],
            [0, [new Number()]],
            [.5, [new Number()]],
            [-20, [new Number()]],
            ['20', [new Number()]],
            [25.45, [new Number()]],
            ['25,45', [new Number()]],
            ['-1.23', [new Number()]],
            ['-4.423e-12', [new Number()]],
            ['12E3', [new Number()]],

            [20, [new Number(asInteger: true)]],
            [0, [new Number(asInteger: true)]],
            ['20', [new Number(asInteger: true)]],
            ['020', [new Number(asInteger: true)]],
            [0x14, [new Number(asInteger: true)]],
            ['5.5e1', [new Number()]],

            [1, [new Number(min: 1)]],
            [PHP_INT_MAX + 1, [new Number(min: 1)]],

            [1, [new Number(asInteger: true, min: 1)]],

            [1, [new Number(max: 1)]],
            [1, [new Number(max: 1.25)]],
            ['22e-12', [new Number(max: 1.25)]],
            ['125e-2', [new Number(max: 1.25)]],
            [1, [new Number(asInteger: true, max: 1.25)]],

            [0, [new Number(min: -10, max: 20)]],
            [-10, [new Number(min: -10, max: 20)]],

            [0, [new Number(asInteger: true, min: -10, max: 20)]],
        ];
    }

    public function dataValidationFailed(): array
    {
        $notANumberMessage = 'Value must be a number.';
        $notAnIntegerMessage = 'Value must be an integer.';

        return [
            ['12:45', [new Number()], ['' => [$notANumberMessage]]],

            [false, [new Number()], ['' => [$notANumberMessage]]],
            [true, [new Number()], ['' => [$notANumberMessage]]],

            ['e12', [new Number()], ['' => [$notANumberMessage]]],
            ['-e3', [new Number()], ['' => [$notANumberMessage]]],
            ['-4.534-e-12', [new Number()], ['' => [$notANumberMessage]]],
            ['12.23^4', [new Number()], ['' => [$notANumberMessage]]],
            ['43^32', [new Number()], ['' => [$notANumberMessage]]],

            [[1, 2, 3], [new Number()], ['' => [$notANumberMessage]]],
            [new stdClass(), [new Number()], ['' => [$notANumberMessage]]],
            [fopen('php://stdin', 'rb'), [new Number()], ['' => [$notANumberMessage]]],

            [25.45, [new Number(asInteger: true)], ['' => [$notAnIntegerMessage]]],
            ['25,45', [new Number(asInteger: true)], ['' => [$notAnIntegerMessage]]],
            ['0x14', [new Number(asInteger: true)], ['' => [$notAnIntegerMessage]]],

            ['-1.23', [new Number(asInteger: true)], ['' => [$notAnIntegerMessage]]],
            ['-4.423e-12', [new Number(asInteger: true)], ['' => [$notAnIntegerMessage]]],
            ['12E3', [new Number(asInteger: true)], ['' => [$notAnIntegerMessage]]],
            ['e12', [new Number(asInteger: true)], ['' => [$notAnIntegerMessage]]],
            ['-e3', [new Number(asInteger: true)], ['' => [$notAnIntegerMessage]]],
            ['-4.534-e-12', [new Number(asInteger: true)], ['' => [$notAnIntegerMessage]]],
            ['12.23^4', [new Number(asInteger: true)], ['' => [$notAnIntegerMessage]]],

            [-1, [new Number(min: 1)], ['' => ['Value must be no less than 1.']]],
            ['22e-12', [new Number(min: 1)], ['' => ['Value must be no less than 1.']]],

            [-1, [new Number(asInteger: true, min: 1)], ['' => ['Value must be no less than 1.']]],
            ['22e-12', [new Number(asInteger: true, min: 1)], ['' => [$notAnIntegerMessage]]],
            [1.5, [new Number(max: 1.25)], ['' => ['Value must be no greater than 1.25.']]],

            // TODO: fix wrong message
            [1.5, [new Number(asInteger: true, max: 1.25)], ['' => [$notAnIntegerMessage]]],
            ['22e-12', [new Number(asInteger: true, max: 1.25)], ['' => [$notAnIntegerMessage]]],
            ['125e-2', [new Number(asInteger: true, max: 1.25)], ['' => [$notAnIntegerMessage]]],

            [-11, [new Number(min: -10, max: 20)], ['' => ['Value must be no less than -10.']]],
            [21, [new Number(min: -10, max: 20)], ['' => ['Value must be no greater than 20.']]],
            [-11, [new Number(asInteger: true, min: -10, max: 20)], ['' => ['Value must be no less than -10.']]],
            [22, [new Number(asInteger: true, min: -10, max: 20)], ['' => ['Value must be no greater than 20.']]],
            ['20e-1', [new Number(asInteger: true, min: -10, max: 20)], ['' => [$notAnIntegerMessage]]],
            'custom error' => [
                0,
                [new Number(min: 5, tooSmallMessage: 'Value is too small.')],
                ['' => ['Value is too small.']],
            ],
        ];
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Number::class, NumberHandler::class];
    }
}
