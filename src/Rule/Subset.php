<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use Yiisoft\Validator\Rule\Trait\RuleNameTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\WhenInterface;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Subset implements SerializableRuleInterface, SkipOnErrorInterface, WhenInterface, SkipOnEmptyInterface
{
    use RuleNameTrait;
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    public function __construct(
        private iterable $values,
        /**
         * @var bool whether the comparison is strict (both type and value must be the same)
         */
        private bool $strict = false,
        private string $iterableMessage = 'Value must be iterable.',
        private string $subsetMessage = 'Values must be ones of {values}.',

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

    public function getValues(): iterable
    {
        return $this->values;
    }

    public function isStrict(): bool
    {
        return $this->strict;
    }

    public function getIterableMessage(): string
    {
        return $this->iterableMessage;
    }

    public function getSubsetMessage(): string
    {
        return $this->subsetMessage;
    }

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
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ];
    }

    public function getHandlerClassName(): string
    {
        return SubsetHandler::class;
    }
}
