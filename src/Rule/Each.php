<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\Validator\AfterInitAttributeEventInterface;
use Yiisoft\Validator\Helper\RulesNormalizer;
use Yiisoft\Validator\PropagateOptionsInterface;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\Helper\RulesDumper;
use Yiisoft\Validator\RuleWithOptionsInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\WhenInterface;

/**
 * Validates an array by checking each of its elements against a set of rules.
 *
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Each implements
    RuleWithOptionsInterface,
    SkipOnErrorInterface,
    WhenInterface,
    SkipOnEmptyInterface,
    PropagateOptionsInterface,
    AfterInitAttributeEventInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @var iterable<RuleInterface>
     */
    private iterable $rules;

    private ?RulesDumper $rulesDumper = null;

    public function __construct(
        /**
         * @param callable|iterable<callable|RuleInterface>|RuleInterface $rules
         */
        iterable|callable|RuleInterface $rules = [],
        private string $incorrectInputMessage = 'Value must be array or iterable.',
        private string $incorrectInputKeyMessage = 'Every iterable key must have an integer or a string type.',

        /**
         * @var bool|callable|null
         */
        private $skipOnEmpty = null,
        private bool $skipOnError = false,
        /**
         * @var WhenType
         */
        private Closure|null $when = null,
    ) {
        $this->rules = RulesNormalizer::normalizeList($rules);
    }

    public function getName(): string
    {
        return 'each';
    }

    public function propagateOptions(): void
    {
        $rules = [];
        foreach ($this->rules as $rule) {
            if ($rule instanceof SkipOnEmptyInterface) {
                $rule = $rule->skipOnEmpty($this->skipOnEmpty);
            }
            if ($rule instanceof SkipOnErrorInterface) {
                $rule = $rule->skipOnError($this->skipOnError);
            }
            if ($rule instanceof WhenInterface) {
                $rule = $rule->when($this->when);
            }

            $rules[] = $rule;

            if ($rule instanceof PropagateOptionsInterface) {
                $rule->propagateOptions();
            }
        }

        $this->rules = $rules;
    }

    /**
     * @return iterable<Closure|Closure[]|RuleInterface|RuleInterface[]>
     */
    public function getRules(): iterable
    {
        return $this->rules;
    }

    public function getIncorrectInputMessage(): string
    {
        return $this->incorrectInputMessage;
    }

    public function getIncorrectInputKeyMessage(): string
    {
        return $this->incorrectInputKeyMessage;
    }

    #[ArrayShape([
        'incorrectInputMessage' => 'array',
        'incorrectInputKeyMessage' => 'array',
        'skipOnEmpty' => 'bool',
        'skipOnError' => 'bool',
        'rules' => 'array',
    ])]
    public function getOptions(): array
    {
        return [
            'incorrectInputMessage' => [
                'template' => $this->incorrectInputMessage,
                'parameters' => [],
            ],
            'incorrectInputKeyMessage' => [
                'template' => $this->incorrectInputKeyMessage,
                'parameters' => [],
            ],
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
            'rules' => $this->getRulesDumper()->asArray($this->rules),
        ];
    }

    public function getHandler(): string
    {
        return EachHandler::class;
    }

    public function afterInitAttribute(object $object, int $target): void
    {
        foreach ($this->rules as $rule) {
            if ($rule instanceof AfterInitAttributeEventInterface) {
                $rule->afterInitAttribute(
                    $object,
                    $target === Attribute::TARGET_CLASS ? Attribute::TARGET_PROPERTY : $target
                );
            }
        }
    }

    private function getRulesDumper(): RulesDumper
    {
        if ($this->rulesDumper === null) {
            $this->rulesDumper = new RulesDumper();
        }

        return $this->rulesDumper;
    }
}
