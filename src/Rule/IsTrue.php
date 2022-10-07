<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\Rule\Trait\RuleNameTrait;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\WhenInterface;

/**
 * Checks if the value is a "true" boolean value or a value corresponding to it. Useful for user agreements etc.
 *
 * @see IsTrueHandler
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class IsTrue implements SerializableRuleInterface, SkipOnErrorInterface, WhenInterface, SkipOnEmptyInterface
{
    use RuleNameTrait;
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    public function __construct(
        /**
         * @var mixed the value representing "true" status. Defaults to `1`.
         */
        private mixed $trueValue = '1',
        /**
         * @var bool whether the comparison to {@see $trueValue} is strict. When this is "true", the value and type must
         * both match {@see $trueValue}. Defaults to "false", meaning only the value needs to be matched.
         */
        private bool $strict = false,
        private string $message = 'The value must be "{true}".',

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

    public function getTrueValue(): mixed
    {
        return $this->trueValue;
    }

    public function isStrict(): bool
    {
        return $this->strict;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getOptions(): array
    {
        return [
            'trueValue' => $this->trueValue,
            'strict' => $this->strict,
            'message' => [
                'message' => $this->message,
                'parameters' => [
                    'true' => $this->trueValue === true ? 'true' : $this->trueValue,
                ],
            ],
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ];
    }

    public function getHandlerClassName(): string
    {
        return IsTrueHandler::class;
    }
}
