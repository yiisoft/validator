<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Type;

use Stringable;
use Yiisoft\Validator\PropertyTranslator\ArrayPropertyTranslator;
use Yiisoft\Validator\PropertyTranslatorInterface;
use Yiisoft\Validator\PropertyTranslatorProviderInterface;
use Yiisoft\Validator\Rule\Type\StringType;
use Yiisoft\Validator\Rule\Type\StringTypeHandler;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class StringTypeTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new StringType();
        $this->assertSame(StringType::class, $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            'default' => [
                new StringType(),
                [
                    'message' => [
                        'template' => '{Property} must be a string.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            'custom' => [
                new StringType(message: 'Custom message.', skipOnEmpty: true, skipOnError: true),
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
            'empty' => ['', new StringType()],
            'single quotes' => ['test', new StringType()],
            'double quotes' => ['test', new StringType()],
            'heredoc syntax' => [
                <<<END
a
b
c
\n
END,
                new StringType(),
            ],
            'nowdoc syntax' => [
                <<<'EOD'
a
b
\\
\
EOD,
                new StringType(),
            ],
            'using as attribute' => [
                new class () {
                    #[StringType]
                    private string $name = 'test';
                },
                null,
            ],
        ];
    }

    public function dataValidationFailed(): array
    {
        $message = 'Value must be a string.';

        return [
            'boolean' => [false, new StringType(), ['' => [$message]]],
            'float' => [1.5, new StringType(), ['' => [$message]]],
            'ingeter' => [1, new StringType(), ['' => [$message]]],
            'stringable' => [
                new class () implements Stringable {
                    public function __toString(): string
                    {
                        return 'test';
                    }
                },
                new StringType(),
                ['' => [$message]],
            ],
            'array' => [[], new StringType(), ['' => [$message]]],
            'message, custom' => [
                ['name' => []],
                ['name' => new StringType('Property - {property}, type - {type}')],
                ['name' => ['Property - name, type - array']],
            ],
            'message, translated property' => [
                new class () implements RulesProviderInterface, PropertyTranslatorProviderInterface {
                    public function __construct(
                        public ?string $name = null,
                    ) {
                    }

                    public function getPropertyLabels(): array
                    {
                        return [
                            'name' => 'Название',
                        ];
                    }

                    public function getPropertyTranslator(): ?PropertyTranslatorInterface
                    {
                        return new ArrayPropertyTranslator($this->getPropertyLabels());
                    }

                    public function getRules(): array
                    {
                        return [
                            'name' => new StringType(message: '"{property}" - не строка.'),
                        ];
                    }
                },
                null,
                ['name' => ['"Название" - не строка.']],
            ],
            'using as attribute' => [
                new class () {
                    #[StringType]
                    private array $name = ['test'];
                },
                null,
                ['name' => ['Name must be a string.']],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new StringType(), new StringType(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new StringType(), new StringType(when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [StringType::class, StringTypeHandler::class];
    }
}
