<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\BeforeValidationInterface;
use Yiisoft\Validator\Rule\Trait\HandlerClassNameTrait;
use Yiisoft\Validator\Rule\Trait\BeforeValidationTrait;
use Yiisoft\Validator\Rule\Trait\RuleNameTrait;
use Yiisoft\Validator\ValidationContext;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Subset implements SerializableRuleInterface, BeforeValidationInterface
{
    use BeforeValidationTrait;
    use HandlerClassNameTrait;
    use RuleNameTrait;

    public function __construct(
        private iterable $values,
        /**
         * @var bool whether the comparison is strict (both type and value must be the same)
         */
        private bool $strict = false,
        private string $iterableMessage = 'Value must be iterable.',
        private string $subsetMessage = 'Values must be ones of {values}.',
        private bool $skipOnEmpty = false,
        private bool $skipOnError = false,
        /**
         * @var Closure(mixed, ValidationContext):bool|null
         */
        private ?Closure $when = null,
    ) {
    }

    /**
     * @return iterable
     */
    public function getValues(): iterable
    {
        return $this->values;
    }

    /**
     * @return bool
     */
    public function isStrict(): bool
    {
        return $this->strict;
    }

    /**
     * @return string
     */
    public function getIterableMessage(): string
    {
        return $this->iterableMessage;
    }

    /**
     * @return string
     */
    public function getSubsetMessage(): string
    {
        return $this->subsetMessage;
    }

    #[ArrayShape([
        'values' => 'iterable',
        'strict' => 'bool',
        'iterableMessage' => 'string[]',
        'subsetMessage' => 'string[]',
        'skipOnEmpty' => 'bool',
        'skipOnError' => 'bool',
    ])]
    public function getOptions(): array
    {
        return [
            'values' => $this->values,
            'strict' => $this->strict,
            'iterableMessage' => [
                'message' => $this->iterableMessage,
            ],
            'subsetMessage' => [
                'message' => $this->subsetMessage,
            ],
            'skipOnEmpty' => $this->skipOnEmpty,
            'skipOnError' => $this->skipOnError,
        ];
    }
}
