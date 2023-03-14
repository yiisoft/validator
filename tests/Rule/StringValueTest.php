<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use stdClass;
use Yiisoft\Validator\Rule\StringValue;
use Yiisoft\Validator\Rule\StringValueHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class StringValueTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new StringValue();
        $this->assertSame('string', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new StringValue(),
                [
                    'message' => [
                        'template' => 'The value must be a string.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new StringValue(message: 'Custom message.', skipOnEmpty: true, skipOnError: true),
                [
                    'message' => [
                        'template' => 'Custom message.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => true,
                    'skipOnError' => true,
                ],
            ],
        ];
    }

    public function dataValidationPassed(): array
    {
        $rule = new StringValue();

        return [
            ['', [$rule]],
            [' ', [$rule]],
            ['test', [$rule]],
        ];
    }

    public function dataValidationFailed(): array
    {
        $rule = new StringValue();
        $message = 'The value must be a string.';

        return [
            [null, [$rule], ['' => [$message]]],
            [1, [$rule], ['' => [$message]]],
            [1.5, [$rule], ['' => [$message]]],
            [false, [$rule], ['' => [$message]]],
            [['test'], [$rule], ['' => [$message]]],
            [new stdClass(), [$rule], ['' => [$message]]],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new StringValue(), new StringValue(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new StringValue(), new StringValue(when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [StringValue::class, StringValueHandler::class];
    }

    protected function getRuleClass(): string
    {
        return StringValue::class;
    }
}
