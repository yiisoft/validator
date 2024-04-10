<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\RuleWithOptionsInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\WhenInterface;

/**
 * Allows to define a set of rules for validating uniqueness of each element of an iterable.
 *
 * @see EachHandler Corresponding handler performing the actual validation.
 *
 * @psalm-import-type SkipOnEmptyValue from SkipOnEmptyInterface
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Unique implements
    RuleWithOptionsInterface,
    SkipOnEmptyInterface,
    SkipOnErrorInterface,
    WhenInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @param string $incorrectInputMessage Error message used when validation fails because the validated value is not
     * an iterable.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{type}`: the type of the value being validated.
     * @param string $incorrectItemValueMessage Error message used when validation fails because the validated iterable
     * contains items with invalid values. Only the following types are allowed: scalar (string, integer, float,
     * boolean), `null`, objects implementing `\Stringable` or `\DateTimeInterface` interfaces.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{type}`: the type of the iterable key being validated.
     * @param string $message Error message used when validation fails because the validated iterable contains duplicate
     * items.
     *
     * You may use the following placeholders in the message:
     *
     *  - `{attribute}`: the translated label of the attribute being validated.
     * @param bool|callable|null $skipOnEmpty Whether to skip this `Each` rule with all defined {@see $rules} if the
     * validated value is empty / not passed. See {@see SkipOnEmptyInterface}.
     * @param bool $skipOnError Whether to skip this `Each` rule with all defined {@see $rules} if any of the previous
     * rules gave an error. See {@see SkipOnErrorInterface}.
     * @param Closure|null $when A callable to define a condition for applying this `Each` rule with all defined
     * {@see $rules}. See {@see WhenInterface}.
     *
     * @psalm-param SkipOnEmptyValue $skipOnEmpty
     * @psalm-param WhenType $when
     */
    public function __construct(
        private string $incorrectInputMessage = 'Value must be array or iterable.',
        private string $incorrectItemValueMessage = 'The allowed types for iterable\'s item values are integer, ' .
        'float, string, boolean, null and object implementing \Stringable or \DateTimeInterface.',
        private string $message = 'Every iterable\'s item must be unique.',
        private mixed $skipOnEmpty = null,
        private bool $skipOnError = false,
        private Closure|null $when = null,
    ) {
    }

    public function getName(): string
    {
        return 'unique';
    }

    /**
     * Gets error message used when validation fails because the validated value is not an iterable.
     *
     * @return string Error message / template.
     *
     * @see $incorrectInputMessage
     */
    public function getIncorrectInputMessage(): string
    {
        return $this->incorrectInputMessage;
    }

    /**
     * Error message used when validation fails because the validated iterable contains items with invalid values.
     *
     * @return string Error message / template.
     *
     * @see $incorrectItemValueMessage
     */
    public function getIncorrectItemValueMessage(): string
    {
        return $this->incorrectItemValueMessage;
    }

    /**
     * Error message used when validation fails because the validated iterable contains duplicate items.
     *
     * @return string Error message / template.
     *
     * @see $message
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    #[ArrayShape([
        'incorrectInputMessage' => 'array',
        'incorrectIItemValueMessage' => 'array',
        'message' => 'array',
        'skipOnEmpty' => 'bool',
        'skipOnError' => 'bool',
    ])]
    public function getOptions(): array
    {
        return [
            'incorrectInputMessage' => [
                'template' => $this->incorrectInputMessage,
                'parameters' => [],
            ],
            'incorrectItemValueMessage' => [
                'template' => $this->incorrectItemValueMessage,
                'parameters' => [],
            ],
            'message' => [
                'template' => $this->message,
                'parameters' => [],
            ],
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ];
    }

    public function getHandler(): string
    {
        return UniqueHandler::class;
    }
}
