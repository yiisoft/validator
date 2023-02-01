<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use Yiisoft\Validator\LimitInterface;
use Yiisoft\Validator\Rule\Trait\LimitTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\RuleWithOptionsInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\WhenInterface;

/**
 * Defines validation options to check that the value is a string of a certain length.
 *
 * @see LengthHandler
 *
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Length implements
    RuleWithOptionsInterface,
    SkipOnErrorInterface,
    WhenInterface,
    SkipOnEmptyInterface,
    LimitInterface
{
    use LimitTrait;
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @param int|null $min Minimum length. `null` means no minimum length limit. Can't be combined with
     * {@see $exactly}. See {@see $lessThanMinMessage} for the customized message for a too short string.
     * @param int|null $max maximum length. `null` means no maximum length limit. Can't be combined with
     * {@see $exactly}. See {@see $greaterThanMaxMessage} for the customized message for a too long string.
     * @param int|null $exactly Exact length. `null` means no strict comparison. Mutually exclusive with
     * {@see $min} and {@see $max}.
     * @param string $incorrectInputMessage Error message used when the value is not a string.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{type}`: the type of the value being validated.
     * @param string $lessThanMinMessage Error message used when the length of the value is smaller than {@see $min}.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{min}`: minimum number of items required.
     * - `{number}`: actual number of items.
     * @param string $greaterThanMaxMessage Error message used when the length of the value is greater than {@see $max}.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{max}`: maximum number of items required.
     * - `{number}`: actual number of items.
     * @param string $notExactlyMessage Error message used when the number of items does not equal {@see $exactly}.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{exactly}`: exact number of items required.
     * - `{number}`: actual number of items.
     * @param string $encoding The encoding of the string value to be validated (e.g. 'UTF-8').
     * If this property is not set, application wide encoding will be used.
     * @param bool|callable|null $skipOnEmpty Whether to skip this rule if the value validated is empty.
     * See {@see SkipOnEmptyInterface}.
     * @param bool $skipOnError Whether to skip this rule if any of the previous rules gave an error.
     * See {@see SkipOnErrorInterface}.
     * @param Closure|null $when A callable to define a condition for applying the rule.
     * See {@see WhenInterface}.
     * @psalm-param WhenType $when
     */
    public function __construct(
        int|null $min = null,
        int|null $max = null,
        int|null $exactly = null,
        private string $incorrectInputMessage = 'This value must be a string.',
        string $lessThanMinMessage = 'This value must contain at least {min, number} {min, plural, one{character} ' .
        'other{characters}}.',
        string $greaterThanMaxMessage = 'This value must contain at most {max, number} {max, plural, one{character} ' .
        'other{characters}}.',
        string $notExactlyMessage = 'This value must contain exactly {exactly, number} {exactly, plural, ' .
        'one{character} other{characters}}.',
        private string $encoding = 'UTF-8',
        private mixed $skipOnEmpty = null,
        private bool $skipOnError = false,
        private Closure|null $when = null
    ) {
        $this->initLimitProperties(
            $min,
            $max,
            $exactly,
            $lessThanMinMessage,
            $greaterThanMaxMessage,
            $notExactlyMessage
        );
    }

    public function getName(): string
    {
        return 'length';
    }

    /**
     * Get error message used when the value is neither an array nor implementing {@see \Countable} interface.
     *
     * @return string Error message.
     *
     * @see $incorrectInputMessage
     */
    public function getIncorrectInputMessage(): string
    {
        return $this->incorrectInputMessage;
    }

    /**
     * Get the encoding of the string value to be validated (e.g. 'UTF-8').
     * If this property is not set, application wide encoding will be used.
     *
     * @return string Encoding of the string value to be validated.
     *
     * @see $encoding
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    public function getOptions(): array
    {
        return array_merge($this->getLimitOptions(), [
            'incorrectInputMessage' => [
                'template' => $this->incorrectInputMessage,
                'parameters' => [],
            ],
            'encoding' => $this->encoding,
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ]);
    }

    public function getHandler(): string
    {
        return LengthHandler::class;
    }
}
