<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Closure;
use Yiisoft\Validator\EmptyCondition\NeverEmpty;
use Yiisoft\Validator\EmptyCondition\WhenNull;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\RequiredHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;
use Yiisoft\Validator\ValidationContext;

final class RequiredTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testDefaultValues(): void
    {
        $rule = new Required();

        $this->assertNull($rule->getEmptyCondition());
        $this->assertSame(RequiredHandler::class, $rule->getHandler());
        $this->assertSame('{Property} cannot be blank.', $rule->getMessage());
        $this->assertSame(Required::class, $rule->getName());
        $this->assertSame('{Property} not passed.', $rule->getNotPassedMessage());
        $this->assertNull($rule->getWhen());
        $this->assertFalse($rule->shouldSkipOnError());
    }

    public function dataGetEmptyCondition(): array
    {
        return [
            'skip on null' => [new WhenNull(), WhenNull::class],
            'closure' => [static fn () => false, Closure::class],
        ];
    }

    /**
     * @dataProvider dataGetEmptyCondition
     */
    public function testGetEmptyCondition(?callable $callback, string $expectedCallbackClassName): void
    {
        $rule = new Required(emptyCondition: $callback);

        $this->assertInstanceOf($expectedCallbackClassName, $rule->getEmptyCondition());
    }

    public function dataOptions(): array
    {
        return [
            [
                new Required(),
                [
                    'message' => [
                        'template' => '{Property} cannot be blank.',
                        'parameters' => [],
                    ],
                    'notPassedMessage' => [
                        'template' => '{Property} not passed.',
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
                [new Required(emptyCondition: new WhenNull())],
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
                [new Required(emptyCondition: static fn (mixed $value): bool => $value === '42')],
                $singleMessageCannotBeBlank,
            ],
            'custom error' => [null, [new Required(message: 'Custom error')], ['' => ['Custom error']]],
            'empty after trimming' => [' ', [new Required()], $singleMessageCannotBeBlank],
            'custom message with property' => [
                ['name' => ''],
                ['name' => new Required(message: '{property} is bad.')],
                ['name' => ['name is bad.']],
            ],
            'custom not passed message with property' => [
                [],
                ['name' => new Required(notPassedMessage: 'Field "{property}" is not passed.')],
                ['name' => ['Field "name" is not passed.']],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new Required(), new Required(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn(mixed $value): bool => $value !== null;
        $this->testWhenInternal(new Required(), new Required(when: $when));
    }

    public function testDefaultEmptyCondition(): void
    {
        $handler = new RequiredHandler(defaultEmptyCondition: new NeverEmpty());

        $result = $handler->validate('', new Required(), new ValidationContext());

        $this->assertTrue($result->isValid());
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Required::class, RequiredHandler::class];
    }
}
