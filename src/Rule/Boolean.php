<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use Yiisoft\Validator\BeforeValidationInterface;
use Yiisoft\Validator\Rule\Trait\BeforeValidationTrait;
use Yiisoft\Validator\Rule\Trait\RuleNameTrait;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * Checks if the value is a boolean value or a value corresponding to it.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Boolean implements SerializableRuleInterface, BeforeValidationInterface
{
    use BeforeValidationTrait;
    use RuleNameTrait;

    public function __construct(
        /**
         * @var mixed the value representing "true" status. Defaults to `1`.
         */
        private mixed $trueValue = '1',
        /**
         * @var mixed the value representing "false" status. Defaults to `0`.
         */
        private mixed $falseValue = '0',
        /**
         * @var bool whether the comparison to {@see $trueValue} and {@see $falseValue} is strict. When this is `true`,
         * the value and type must both match those of {@see $trueValue} or {@see $falseValue}. Defaults to `false`,
         * meaning only the value needs to be matched.
         */
        private bool $strict = false,
        private string $message = 'The value must be either "{true}" or "{false}".',

        /**
         * @var bool|callable
         */
        $skipOnEmpty = false,
        private bool $skipOnError = false,
        /**
         * @var Closure(mixed, ValidationContext):bool|null
         */
        private ?Closure $when = null,
    ) {
        $this->setSkipOnEmptyCallback($skipOnEmpty);
    }

    /**
     * @return mixed
     */
    public function getTrueValue(): mixed
    {
        return $this->trueValue;
    }

    /**
     * @return mixed
     */
    public function getFalseValue(): mixed
    {
        return $this->falseValue;
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
    public function getMessage(): string
    {
        return $this->message;
    }

    public function getOptions(): array
    {
        return [
            'trueValue' => $this->trueValue,
            'falseValue' => $this->falseValue,
            'strict' => $this->strict,
            'message' => [
                'message' => $this->message,
                'parameters' => [
                    'true' => $this->trueValue === true ? 'true' : $this->trueValue,
                    'false' => $this->falseValue === false ? 'false' : $this->falseValue,
                ],
            ],
            'skipOnEmpty' => $this->skipOnEmptyCallback !== null,
            'skipOnError' => $this->skipOnError,
        ];
    }

    public function getHandlerClassName(): string
    {
        return BooleanHandler::class;
    }
}
