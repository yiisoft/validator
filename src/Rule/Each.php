<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\Validator\BeforeValidationInterface;
use Yiisoft\Validator\PropagateOptionsInterface;
use Yiisoft\Validator\Rule\Trait\BeforeValidationTrait;
use Yiisoft\Validator\Rule\Trait\RuleNameTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * Validates an array by checking each of its elements against a set of rules.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Each implements
    SerializableRuleInterface,
    BeforeValidationInterface,
    SkipOnEmptyInterface,
    PropagateOptionsInterface
{
    use BeforeValidationTrait;
    use RuleNameTrait;
    use SkipOnEmptyTrait;

    public function __construct(
        /**
         * @var iterable<RuleInterface|SkipOnEmptyInterface|BeforeValidationInterface>
         */
        private iterable $rules = [],
        private string $incorrectInputMessage = 'Value must be array or iterable.',
        private string $message = '{error} {value} given.',

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

    public function propagateOptions(): void
    {
        $rules = [];
        foreach ($this->rules as $rule) {
            $rule = $rule->skipOnEmpty($this->skipOnEmpty);
            $rule = $rule->skipOnError($this->skipOnError);
            $rule = $rule->when($this->when);

            $rules[] = $rule;

            if ($rule instanceof PropagateOptionsInterface) {
                $rule->propagateOptions();
            }
        }

        $this->rules = $rules;
    }

    /**
     * @return iterable<\Closure|\Closure[]|RuleInterface|RuleInterface[]|SkipOnEmptyInterface|SkipOnEmptyInterface[]|BeforeValidationInterface|BeforeValidationInterface[]>
     */
    public function getRules(): iterable
    {
        return $this->rules;
    }

    /**
     * @return string
     */
    public function getIncorrectInputMessage(): string
    {
        return $this->incorrectInputMessage;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    #[ArrayShape([
        'incorrectInputMessage' => 'array',
        'message' => 'array',
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
                'message' => $this->getIncorrectInputMessage(),
            ],
            'message' => [
                'message' => $this->getMessage(),
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
