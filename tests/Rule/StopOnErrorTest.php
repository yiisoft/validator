<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\StopOnError;
use Yiisoft\Validator\Rule\StopOnErrorHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class StopOnErrorTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new StopOnError([new HasLength(min: 10)]);
        $this->assertSame('stopOnError', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new StopOnError([new HasLength(min: 10)]),
                [
                    'skipOnEmpty' => false,
                    'rules' => [
                        [
                            'hasLength',
                            'min' => 10,
                            'max' => null,
                            'exactly' => null,
                            'lessThanMinMessage' => [
                                'template' => 'This value must contain at least {min, number} {min, plural, ' .
                                    'one{character} other{characters}}.',
                                'parameters' => [
                                    'min' => 10,
                                ],
                            ],
                            'greaterThanMaxMessage' => [
                                'template' => 'This value must contain at most {max, number} {max, plural, ' .
                                    'one{character} other{characters}}.',
                                'parameters' => [
                                    'max' => null,
                                ],
                            ],
                            'notExactlyMessage' => [
                                'template' => 'This value must contain exactly {exactly, number} {exactly, plural, ' .
                                    'one{character} other{characters}}.',
                                'parameters' => [
                                    'exactly' => null,
                                ],
                            ],
                            'incorrectInputMessage' => [
                                'template' => 'This value must be a string.',
                                'parameters' => [],
                            ],
                            'encoding' => 'UTF-8',
                            'skipOnEmpty' => false,
                            'skipOnError' => false,
                        ],
                    ],
                ],
            ],
        ];
    }

    public function dataValidationPassed(): array
    {
        return [
            'at least one succeed property' => [
                'hello',
                [
                    new StopOnError([
                        new HasLength(min: 1),
                        new HasLength(max: 10),
                    ]),
                ],
            ],
        ];
    }

    public function dataValidationFailed(): array
    {
        return [
            'case1' => [
                'hello',
                [
                    new StopOnError([
                        new HasLength(min: 10),
                        new HasLength(max: 1),
                    ]),
                ],
                ['' => ['This value must contain at least 10 characters.']],
            ],
            'case2' => [
                'hello',
                [
                    new StopOnError([
                        new HasLength(max: 1),
                        new HasLength(min: 10),
                    ]),
                ],
                ['' => ['This value must contain at most 1 character.']],
            ],
            'case3' => [
                'hello',
                [
                    new Number(),
                    new StopOnError([
                        new HasLength(max: 1),
                        new HasLength(min: 10),
                    ]),
                    new HasLength(min: 7),
                ],
                [
                    '' => [
                        'Value must be a number.',
                        'This value must contain at least 7 characters.',
                    ],
                ],
            ],
        ];
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(
            new StopOnError([new HasLength(min: 10)]),
            new StopOnError([new HasLength(min: 10)], when: $when),
        );
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [StopOnError::class, StopOnErrorHandler::class];
    }
}
