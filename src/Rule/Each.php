<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\Validator\PropagateOptionsInterface;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\WhenInterface;

/**
 * Validates an array by checking each of its elements against a set of rules.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Each implements
    SerializableRuleInterface,
    SkipOnErrorInterface,
    WhenInterface,
    SkipOnEmptyInterface,
    PropagateOptionsInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    public function __construct(
        /**
         * @var iterable<RuleInterface>
         */
        private iterable $rules = [],
        private string $incorrectInputMessage = 'Value must be array or iterable.',
        private string $incorrectInputKeyMessage = 'Every iterable key must have an integer or a string type.',

        /**
         * @var bool|callable|null
         */
        private $skipOnEmpty = null,
        private bool $skipOnError = false,
        /**
         * @var Closure(mixed, ValidationContext):bool|null
         */
        private ?Closure $when = null,
    ) {
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
        $arrayOfRules = [];
        foreach ($this->rules as $rule) {
            if ($rule instanceof SerializableRuleInterface) {
                $arrayOfRules[] = array_merge([$rule->getName()], $rule->getOptions());
            } else {
                $arrayOfRules[] = [$rule->getName()];
            }
        }

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
            'rules' => $arrayOfRules,
        ];
    }

    public function getHandlerClassName(): string
    {
        return EachHandler::class;
    }
}
