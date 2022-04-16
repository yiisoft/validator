<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Boolean;

use Attribute;
use Closure;
use Yiisoft\Validator\Rule\RuleNameTrait;
use Yiisoft\Validator\Rule\HandlerClassNameTrait;
use Yiisoft\Validator\RuleInterface;

/**
 * Checks if the value is a boolean value or a value corresponding to it.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Boolean implements RuleInterface
{
    use HandlerClassNameTrait;
    use RuleNameTrait;

    public function __construct(
        /**
         * @var mixed the value representing true status. Defaults to '1'.
         */
        public          $trueValue = '1',
        /**
         * @var mixed the value representing false status. Defaults to '0'.
         */
        public          $falseValue = '0',
        /**
         * @var bool whether the comparison to {@see $trueValue} and {@see $falseValue} is strict.
         * When this is `true`, the value and type must both match those of {@see $trueValue} or
         * {@see $falseValue}. Defaults to `false`, meaning only the value needs to be matched.
         */
        public bool $strict = false,
        public string $message = 'The value must be either "{true}" or "{false}".',
        public bool $skipOnEmpty = false,
        public bool $skipOnError = false,
        public ?Closure $when = null,
    ) {
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
                    // TODO: get reasons to do like this
                    //                    'true' => $this->trueValue === true ? 'true' : $this->trueValue,
                    //                    'false' => $this->falseValue === false ? 'false' : $this->falseValue,
                    'true' => $this->trueValue,
                    'false' => $this->falseValue,
                ],
            ],
            'skipOnEmpty' => $this->skipOnEmpty,
            'skipOnError' => $this->skipOnError,
        ];
    }
}
