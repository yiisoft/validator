<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\RuleWithOptionsInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\WhenInterface;

/**
 * Checks if the value is a boolean value or a value corresponding to it.
 *
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Boolean implements RuleWithOptionsInterface, SkipOnEmptyInterface, SkipOnErrorInterface, WhenInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    public function __construct(
        /**
         * @var scalar the value representing "true" status. Defaults to `1`.
         */
        private int|float|string|bool $trueValue = '1',
        /**
         * @var scalar the value representing "false" status. Defaults to `0`.
         */
        private int|float|string|bool $falseValue = '0',
        /**
         * @var bool whether the comparison to {@see $trueValue} and {@see $falseValue} is strict. When this is `true`,
         * the value and type must both match those of {@see $trueValue} or {@see $falseValue}. Defaults to `false`,
         * meaning only the value needs to be matched.
         */
        private bool $strict = false,
        private string $nonScalarMessage = 'Value must be either "{true}" or "{false}".',
        private string $scalarMessage = 'Value must be either "{true}" or "{false}".',

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
    }

    public function getName(): string
    {
        return 'boolean';
    }

    public function getTrueValue(): int|float|string|bool
    {
        return $this->trueValue;
    }

    public function getFalseValue(): int|float|string|bool
    {
        return $this->falseValue;
    }

    public function isStrict(): bool
    {
        return $this->strict;
    }

    public function getNonScalarMessage(): string
    {
        return $this->nonScalarMessage;
    }

    public function getScalarMessage(): string
    {
        return $this->scalarMessage;
    }

    public function getOptions(): array
    {
        $messageParameters = [
            'true' => $this->trueValue === true ? 'true' : $this->trueValue,
            'false' => $this->falseValue === false ? 'false' : $this->falseValue,
        ];

        return [
            'trueValue' => $this->trueValue,
            'falseValue' => $this->falseValue,
            'strict' => $this->strict,
            'nonScalarMessage' => [
                'template' => $this->nonScalarMessage,
                'parameters' => $messageParameters,
            ],
            'scalarMessage' => [
                'template' => $this->scalarMessage,
                'parameters' => $messageParameters,
            ],
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ];
    }

    public function getHandlerClassName(): string
    {
        return BooleanHandler::class;
    }
}
