<?php

declare(strict_types=1);

namespace Rule\Type;

use Yiisoft\Validator\AttributeTranslator\ArrayAttributeTranslator;
use Yiisoft\Validator\AttributeTranslatorInterface;
use Yiisoft\Validator\AttributeTranslatorProviderInterface;
use Yiisoft\Validator\Rule\Type\BooleanType;
use Yiisoft\Validator\Rule\Type\BooleanTypeHandler;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class BooleanTypeTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new BooleanType();
        $this->assertSame(BooleanType::class, $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            'default' => [
                new BooleanType(),
                [
                    'message' => [
                        'template' => 'Value must be a boolean.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            'custom' => [
                new BooleanType(message: 'Custom message.', skipOnEmpty: true, skipOnError: true),
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
        return [
            'boolean, false' => [false, new BooleanType()],
            'boolean, true' => [true, new BooleanType()],
            'using as attribute' => [
                new class () {
                    #[BooleanType]
                    private bool $active = true;
                },
                null,
            ],
        ];
    }

    public function dataValidationFailed(): array
    {
        $message = 'Value must be a boolean.';

        return [
            'float, zero' => [0.0, new BooleanType(), ['' => [$message]]],
            'float, one' => [1.0, new BooleanType(), ['' => [$message]]],
            'integer, zero' => [0, new BooleanType(), ['' => [$message]]],
            'integer, one' => [1, new BooleanType(), ['' => [$message]]],
            'string, zero' => ['0', new BooleanType(), ['' => [$message]]],
            'string, one' => ['1', new BooleanType(), ['' => [$message]]],
            'string containing false' => ['false', new BooleanType(), ['' => [$message]]],
            'string containing true' => ['true', new BooleanType(), ['' => [$message]]],
            'array' => [[], new BooleanType(), ['' => [$message]]],
            'message, custom' => [
                ['active' => []],
                ['active' => new BooleanType('Attribute - {attribute}, type - {type}')],
                ['active' => ['Attribute - active, type - array']],
            ],
            'message, translated attribute' => [
                new class () implements RulesProviderInterface, AttributeTranslatorProviderInterface {
                    public function __construct(
                        public ?bool $active = null,
                    ) {
                    }

                    public function getAttributeLabels(): array
                    {
                        return [
                            'active' => 'Активен',
                        ];
                    }

                    public function getAttributeTranslator(): ?AttributeTranslatorInterface
                    {
                        return new ArrayAttributeTranslator($this->getAttributeLabels());
                    }

                    public function getRules(): array
                    {
                        return [
                            'active' => new BooleanType(message: '"{attribute}" - не булево значение.'),
                        ];
                    }
                },
                null,
                ['active' => ['"Активен" - не булево значение.']],
            ],
            'using as attribute' => [
                new class () {
                    #[BooleanType]
                    private int $active = 1;
                },
                null,
                ['active' => [$message]],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new BooleanType(), new BooleanType(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new BooleanType(), new BooleanType(when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [BooleanType::class, BooleanTypeHandler::class];
    }
}
