<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\Validator\Rule\Trait\RuleNameTrait;
use Yiisoft\Validator\Rule\Trait\HandlerClassNameTrait;
use Yiisoft\Validator\ParametrizedRuleInterface;

/**
 * Checks if the value is a boolean value or a value corresponding to it.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Boolean implements ParametrizedRuleInterface
{
    use HandlerClassNameTrait;
    use RuleNameTrait;

    public function __construct(
        /**
         * @var mixed the value representing true status. Defaults to '1'.
         */
        private $trueValue = '1',
        /**
         * @var mixed the value representing false status. Defaults to '0'.
         */
        private $falseValue = '0',
        /**
         * @var bool whether the comparison to {@see $trueValue} and {@see $falseValue} is strict.
         * When this is `true`, the value and type must both match those of {@see $trueValue} or
         * {@see $falseValue}. Defaults to `false`, meaning only the value needs to be matched.
         */
        private bool $strict = false,
        private string $message = 'The value must be either "{true}" or "{false}".',
        private bool $skipOnEmpty = false,
        private bool $skipOnError = false,
        private ?Closure $when = null,
    ) {
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

    /**
     * @return bool
     */
    public function isSkipOnEmpty(): bool
    {
        return $this->skipOnEmpty;
    }

    /**
     * @return bool
     */
    public function isSkipOnError(): bool
    {
        return $this->skipOnError;
    }

    /**
     * @return Closure|null
     */
    public function getWhen(): ?Closure
    {
        return $this->when;
    }

    #[ArrayShape([
        'trueValue' => 'string',
        'falseValue' => 'string',
        'strict' => 'bool',
        'message' => 'array',
        'skipOnEmpty' => 'bool',
        'skipOnError' => 'bool',
    ])]
    public function getOptions(): array
    {
        return [
            'trueValue' => $this->trueValue,
            'falseValue' => $this->falseValue,
            'strict' => $this->strict,
            'message' => [
                'message' => $this->message,
                'parameters' => [
                    // TODO: get reasons to do like this
                    //  'true' => $this->trueValue === true ? 'true' : $this->trueValue,
                    //  'false' => $this->falseValue === false ? 'false' : $this->falseValue,
                    'true' => $this->trueValue,
                    'false' => $this->falseValue,
                ],
            ],
            'skipOnEmpty' => $this->skipOnEmpty,
            'skipOnError' => $this->skipOnError,
        ];
    }
}
