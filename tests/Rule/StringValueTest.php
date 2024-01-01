<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use stdClass;
use Yiisoft\Validator\AttributeTranslator\ArrayAttributeTranslator;
use Yiisoft\Validator\AttributeTranslatorInterface;
use Yiisoft\Validator\AttributeTranslatorProviderInterface;
use Yiisoft\Validator\Rule\StringValue;
use Yiisoft\Validator\Rule\StringValueHandler;
use Yiisoft\Validator\RulesProviderInterface;
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
        $this->assertSame(StringValue::class, $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new StringValue(),
                [
                    'message' => [
                        'template' => '{label} must be a string.',
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
            'value: empty string' => ['', [$rule]],
            'value: empty string with whitespaces' => [' ', [$rule]],
            'value: non-empty string' => ['test', [$rule]],
            'value: null, skipOnEmpty: true' => [null, [new StringValue(skipOnEmpty: true)]],
            'value: null, when: custom callable allowing everything except null' => [
                null,
                [new StringValue(when: static fn (mixed $value): bool => $value !== null)],
            ],
            'value: object providing rules and valid data' => [
                new class () {
                    #[StringValue]
                    private string $name = 'test';
                },
                null,
            ],
        ];
    }

    public function dataValidationFailed(): array
    {
        $rule = new StringValue();
        $message = 'The value must be a string.';

        return [
            'value: null' => [null, [$rule], ['' => [$message]]],
            'value: integer' => [1, [$rule], ['' => [$message]]],
            'value: float' => [1.5, [$rule], ['' => [$message]]],
            'value: boolean' => [false, [$rule], ['' => [$message]]],
            'value: array' => [['test'], [$rule], ['' => [$message]]],
            'value: object' => [new stdClass(), [$rule], ['' => [$message]]],
            'value: null, multiple rules' => [
                null,
                [
                    new StringValue(),
                    new StringValue(),
                ],
                ['' => [$message, $message]],
            ],
            'value: null, multiple rules, skipOnError: true' => [
                null,
                [
                    new StringValue(),
                    new StringValue(skipOnError: true),
                ],
                ['' => [$message]],
            ],
            'value: integer, when: custom callable allowing everything except null' => [
                1,
                [new StringValue(when: static fn (mixed $value): bool => $value !== null)],
                ['' => [$message]],
            ],
            'value: object providing rules and wrong data' => [
                new class () {
                    #[StringValue]
                    private ?string $name = null;
                },
                null,
                ['name' => [$message]],
            ],
            'value: boolean, message: custom' => [
                false,
                [new StringValue(message: 'Custom message.')],
                ['' => ['Custom message.']],
            ],
            'value: boolean, message: custom, with parameters' => [
                false,
                [new StringValue(message: 'Attribute - {attribute}, type - {type}.')],
                ['' => ['Attribute - , type - bool.']],
            ],
            'value: boolean, message: custom, with parameters, attribute set' => [
                ['data' => false],
                ['data' => new StringValue(message: 'Attribute - {attribute}, type - {type}.')],
                ['data' => ['Attribute - data, type - bool.']],
            ],
            'value: object providing rules, attribute labels and wrong data' => [
                new class () implements RulesProviderInterface, AttributeTranslatorProviderInterface {
                    public function __construct(
                        public ?string $name = null,
                    ) {
                    }

                    public function getAttributeLabels(): array
                    {
                        return [
                            'name' => 'Имя',
                        ];
                    }

                    public function getAttributeTranslator(): ?AttributeTranslatorInterface
                    {
                        return new ArrayAttributeTranslator($this->getAttributeLabels());
                    }

                    public function getRules(): array
                    {
                        return [
                            'name' => [
                                new StringValue(message: '{attribute} плохое.'),
                            ],
                        ];
                    }
                },
                null,
                ['name' => ['Имя плохое.']],
            ],
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
