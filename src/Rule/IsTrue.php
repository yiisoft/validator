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
 * Checks if the value is a "true" boolean value or a value corresponding to it. Useful for user agreements etc.
 *
 * @see IsTrueHandler
 *
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class IsTrue implements RuleWithOptionsInterface, SkipOnErrorInterface, WhenInterface, SkipOnEmptyInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @const Default message used for all cases.
     */
    private const DEFAULT_MESSAGE = 'The value must be "{true}".';

    public function __construct(
        private int|float|string|bool $trueValue = '1',
        private bool $strict = false,
        private string $messageWithType = self::DEFAULT_MESSAGE,
        private string $messageWithValue = self::DEFAULT_MESSAGE,
        private mixed $skipOnEmpty = null,
        private bool $skipOnError = false,
        private Closure|null $when = null,
    ) {
    }

    public function getName(): string
    {
        return 'isTrue';
    }

    public function getTrueValue(): int|float|string|bool
    {
        return $this->trueValue;
    }

    public function isStrict(): bool
    {
        return $this->strict;
    }

    /**
     * A getter for {@see $messageWithType}.
     *
     * @return string Error message.
     */
    public function getMessageWithType(): string
    {
        return $this->messageWithType;
    }

    /**
     * A getter for {@see $messageWithValue}.
     *
     * @return string Error message.
     */
    public function getMessageWithValue(): string
    {
        return $this->messageWithValue;
    }

    public function getOptions(): array
    {
        $messageParameters = [
            'true' => $this->trueValue === true ? 'true' : $this->trueValue,
        ];

        return [
            'trueValue' => $this->trueValue,
            'strict' => $this->strict,
            'messageWithType' => [
                'template' => $this->messageWithType,
                'parameters' => $messageParameters,
            ],
            'messageWithValue' => [
                'template' => $this->messageWithValue,
                'parameters' => $messageParameters,
            ],
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ];
    }

    public function getHandler(): string
    {
        return IsTrueHandler::class;
    }
}
