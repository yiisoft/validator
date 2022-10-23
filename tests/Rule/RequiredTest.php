<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Closure;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\RequiredHandler;
use Yiisoft\Validator\SkipOnEmptyCallback\SkipOnNull;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SerializableRuleTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;
use Yiisoft\Validator\ValidationContext;

final class RequiredTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use SerializableRuleTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testDefaultValues(): void
    {
        $rule = new Required();

        $this->assertInstanceOf(Closure::class, $rule->getEmptyCallback());
        $this->assertSame(RequiredHandler::class, $rule->getHandlerClassName());
        $this->assertSame('Value cannot be blank.', $rule->getMessage());
        $this->assertSame('required', $rule->getName());
        $this->assertSame('Value not passed.', $rule->getNotPassedMessage());
        $this->assertNull($rule->getWhen());
        $this->assertFalse($rule->shouldSkipOnError());
    }

    public function dataGetEmptyCallback(): array
    {
        return [
            'null' => [null, Closure::class],
            'skip on null' => [new SkipOnNull(), SkipOnNull::class],
            'closure' => [static fn () => false, Closure::class],
        ];
    }

    /**
     * @dataProvider dataGetEmptyCallback
     */
    public function testGetEmptyCallback(?callable $callback, string $expectedCallbackClassName): void
    {
        $rule = new Required(emptyCallback: $callback);

        $this->assertInstanceOf($expectedCallbackClassName, $rule->getEmptyCallback());
    }

    public function dataOptions(): array
    {
        return [
            [
                new Required(),
                [
                    'message' => 'Value cannot be blank.',
                    'notPassedMessage' => 'Value not passed.',
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    public function dataValidationPassed(): array
    {
        return [
            ['not empty', [new Required()]],
            [['with', 'elements'], [new Required()]],
            'skip on null' => [
                '',
                [new Required(emptyCallback: new SkipOnNull())],
            ],
        ];
    }

    public function dataValidationFailed(): array
    {
        $singleMessageCannotBeBlank = ['' => ['Value cannot be blank.']];

        return [
            [null, [new Required()], $singleMessageCannotBeBlank],
            [[], [new Required()], $singleMessageCannotBeBlank],
            'custom empty callback' => [
                '42',
                [new Required(emptyCallback: static fn (mixed $value): bool => $value === '42')],
                $singleMessageCannotBeBlank,
            ],
            'custom error' => [null, [new Required(message: 'Custom error')], ['' => ['Custom error']]],
            'empty after trimming' => [' ', [new Required()], $singleMessageCannotBeBlank],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new Required(), new Required(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value, ValidationContext $context): bool => $value !== null;
        $this->testWhenInternal(new Required(), new Required(when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Required::class, RequiredHandler::class];
    }
}
