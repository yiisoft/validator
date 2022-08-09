<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\Validator\BeforeValidationInterface;
use Yiisoft\Validator\Rule\Trait\BeforeValidationTrait;
use Yiisoft\Validator\Rule\Trait\RuleNameTrait;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * Allows to combine and validate multiple rules.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class Composite implements SerializableRuleInterface, BeforeValidationInterface
{
    use BeforeValidationTrait;
    use RuleNameTrait;

    public function __construct(
        /**
         * @var iterable<RuleInterface>
         */
        private iterable $rules = [],
        private bool $skipOnEmpty = false,
        private $skipOnEmptyCallback = null,
        private bool $skipOnError = false,
        /**
         * @var Closure(mixed, ValidationContext):bool|null
         */
        private ?Closure $when = null,
    ) {
        $this->initSkipOnEmptyProperties($skipOnEmpty, $skipOnEmptyCallback);
    }

    #[ArrayShape([
        'skipOnEmpty' => 'bool',
        'skipOnError' => 'bool',
        'rules' => 'array',
    ])]
    public function getOptions(): array
    {
        $arrayOfRules = [];
        foreach ($this->getRules() as $rule) {
            if ($rule instanceof SerializableRuleInterface) {
                $arrayOfRules[] = array_merge([$rule->getName()], $rule->getOptions());
            } else {
                $arrayOfRules[] = [$rule->getName()];
            }
        }

        return [
            'skipOnEmpty' => $this->skipOnEmpty,
            'skipOnError' => $this->skipOnError,
            'rules' => $arrayOfRules,
        ];
    }

    /**
     * @return iterable<\Closure|\Closure[]|RuleInterface|RuleInterface[]>
     */
    public function getRules(): iterable
    {
        return $this->rules;
    }

    public function getHandlerClassName(): string
    {
        return CompositeHandler::class;
    }
}
