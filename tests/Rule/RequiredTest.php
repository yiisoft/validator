<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Closure;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\RequiredHandler;
use Yiisoft\Validator\EmptyCriteria\WhenEmpty;
use Yiisoft\Validator\EmptyCriteria\WhenNull;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SerializableRuleTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class RequiredTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use SerializableRuleTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testDefaultValues(): void
    {
        $rule = new Required();

        $this->assertInstanceOf(WhenEmpty::class, $rule->getEmptyCriteria());
        $this->assertSame(RequiredHandler::class, $rule->getHandlerClassName());
        $this->assertSame('Value cannot be blank.', $rule->getMessage());
        $this->assertSame('required', $rule->getName());
        $this->assertSame('Value not passed.', $rule->getNotPassedMessage());
        $this->assertNull($rule->getWhen());
        $this->assertFalse($rule->shouldSkipOnError());
    }

    public function dataGetEmptyCriteria(): array
    {
        return [
            'null' => [null, WhenEmpty::class],
            'skip on null' => [new WhenNull(), WhenNull::class],
            'closure' => [static fn () => false, Closure::class],
        ];
    }

    /**
     * @dataProvider dataGetEmptyCriteria
     */
    public function testGetEmptyCriteria(?callable $callback, string $expectedCallbackClassName): void
    {
        $rule = new Required(emptyCriteria: $callback);

        $this->assertInstanceOf($expectedCallbackClassName, $rule->getEmptyCriteria());
    }

    public function dataOptions(): array
    {
        return [
            [
                new Required(),
                [
                    'message' => [
                        'template' => 'Value cannot be blank.',
                        'parameters' => [],
                    ],
                    'notPassedMessage' => [
                        'template' => 'Value not passed.',
                        'parameters' => [],
                    ],
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
                [new Required(emptyCriteria: new WhenNull())],
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
                [new Required(emptyCriteria: static fn (mixed $value): bool => $value === '42')],
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
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new Required(), new Required(when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Required::class, RequiredHandler::class];
    }
}
