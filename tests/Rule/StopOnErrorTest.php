<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\StopOnError;
use Yiisoft\Validator\Rule\StopOnErrorHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SerializableRuleTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class StopOnErrorTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use SerializableRuleTestTrait;
    use SkipOnErrorTestTrait;
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
                    'skipOnError' => false,
                    'rules' => [
                        [
                            'hasLength',
                            'min' => 10,
                            'max' => null,
                            'exactly' => null,
                            'lessThanMinMessage' => [
                                'message' => 'This value must contain at least {min, number} {min, plural, ' .
                                    'one{character} other{characters}}.',
                                'parameters' => [
                                    'min' => 10,
                                ],
                            ],
                            'greaterThanMaxMessage' => [
                                'message' => 'This value must contain at most {max, number} {max, plural, ' .
                                    'one{character} other{characters}}.',
                                'parameters' => [
                                    'max' => null,
                                ],
                            ],
                            'notExactlyMessage' => [
                                'message' => 'This value must contain exactly {exactly, number} {exactly, plural, ' .
                                    'one{character} other{characters}}.',
                                'parameters' => [
                                    'exactly' => null,
                                ],
                            ],
                            'message' => [
                                'message' => 'This value must be a string.',
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
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testskipOnErrorInternal(
            new StopOnError([new HasLength(min: 10)]),
            new StopOnError([new HasLength(min: 10)], skipOnError: true),
        );
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
