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

final class StopOnErrorTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use SerializableRuleTestTrait;
    use SkipOnErrorTestTrait;

    public function testGetName(): void
    {
        $rule = new StopOnError();
        $this->assertSame('stopOnError', $rule->getName());
    }

    public function testSkipOnEmptyInConstructor(): void
    {
        $rule = new StopOnError(skipOnEmpty: true);
        $this->assertTrue($rule->getSkipOnEmpty());
    }

    public function testSkipOnEmptySetter(): void
    {
        $rule = (new StopOnError())->skipOnEmpty(true);
        $this->assertTrue($rule->getSkipOnEmpty());
    }

    public function dataOptions(): array
    {
        return [
            [
                new StopOnError(),
                [
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'rules' => null,
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
            'nested rules instead of plain structure' => [
                'hello',
                [
                    new StopOnError([
                        [
                            new HasLength(max: 1),
                            new HasLength(min: 10),
                        ],
                    ]),
                ],
                ['' => ['This value must contain at most 1 character.']],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testskipOnErrorInternal(new StopOnError(), new StopOnError(skipOnError: true));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [StopOnError::class, StopOnErrorHandler::class];
    }
}
