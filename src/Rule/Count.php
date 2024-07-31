<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use Countable;
use Yiisoft\Validator\CountableLimitInterface;
use Yiisoft\Validator\DumpedRuleInterface;
use Yiisoft\Validator\Rule\Trait\CountableLimitTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\WhenInterface;

/**
 * Defines validation options to check that the value contains certain number of items.
 * Can be applied to arrays or classes implementing {@see Countable} interface.
 *
 * @see CountHandler
 *
 * @psalm-import-type SkipOnEmptyValue from SkipOnEmptyInterface
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Count implements
    DumpedRuleInterface,
    SkipOnErrorInterface,
    WhenInterface,
    SkipOnEmptyInterface,
    CountableLimitInterface
{
    use CountableLimitTrait;
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @param int|null $exactly Exact number of items. `null` means no strict comparison. Mutually exclusive with
     * {@see $min} and {@see $max}.
     * @param int|null $min Minimum number of items. null means no minimum number limit. Can't be combined with
     * {@see $exactly}. See {@see $lessThanMinMessage} for the customized message for a value with too few items.
     * @param int|null $max Maximum number of items. null means no maximum number limit. Can't be combined with
     * {@see $exactly}. See {@see $greaterThanMaxMessage} for the customized message for a value with too many items.
     * @param string $incorrectInputMessage Error message used when the value is neither an array nor an object
     * implementing {@see \Countable} interface.
     *
     * You may use the following placeholders in the message:
     *
     * - `{property}`: the translated label of the property being validated.
     * - `{type}`: the type of the value being validated.
     * @param string $lessThanMinMessage Error message used when the number of items is smaller than {@see $min}.
     *
     * You may use the following placeholders in the message:
     *
     * - `{property}`: the translated label of the property being validated.
     * - `{min}`: minimum number of items required.
     * - `{number}`: actual number of items.
     * @param string $greaterThanMaxMessage Error message used when the number of items is greater than {@see $max}.
     *
     * You may use the following placeholders in the message:
     *
     * - `{property}`: the translated label of the property being validated.
     * - `{max}`: maximum number of items required.
     * - `{number}`: actual number of items.
     * @param string $notExactlyMessage Error message used when the number of items does not equal {@see $exactly}.
     *
     * You may use the following placeholders in the message:
     *
     * - `{property}`: the translated label of the property being validated.
     * - `{exactly}`: exact number of items required.
     * - `{number}`: actual number of items.
     * @param bool|callable|null $skipOnEmpty Whether to skip this rule if the value validated is empty.
     * See {@see SkipOnEmptyInterface}.
     * @param bool $skipOnError Whether to skip this rule if any of the previous rules gave an error.
     * See {@see SkipOnErrorInterface}.
     * @param Closure|null $when A callable to define a condition for applying the rule.
     * See {@see WhenInterface}.
     *
     * @psalm-param SkipOnEmptyValue $skipOnEmpty
     * @psalm-param WhenType $when
     */
    public function __construct(
        int|null $exactly = null,
        int|null $min = null,
        int|null $max = null,
        private string $incorrectInputMessage = '{Property} must be an array or implement \Countable interface. ' .
        '{type} given.',
        string $lessThanMinMessage = '{Property} must contain at least {min, number} {min, plural, one{item} ' .
        'other{items}}.',
        string $greaterThanMaxMessage = '{Property} must contain at most {max, number} {max, plural, one{item} ' .
        'other{items}}.',
        string $notExactlyMessage = '{Property} must contain exactly {exactly, number} {exactly, plural, one{item} ' .
        'other{items}}.',
        bool|callable|null $skipOnEmpty = null,
        private bool $skipOnError = false,
        private Closure|null $when = null,
    ) {
        $this->initCountableLimitProperties(
            $min,
            $max,
            $exactly,
            $lessThanMinMessage,
            $greaterThanMaxMessage,
            $notExactlyMessage
        );
        $this->skipOnEmpty = $skipOnEmpty;
    }

    public function getName(): string
    {
        return self::class;
    }

    /**
     * Get error message used when the value is neither an array nor an object implementing {@see \Countable} interface.
     *
     * @return string Error message.
     *
     * @see $incorrectInputMessage
     */
    public function getIncorrectInputMessage(): string
    {
        return $this->incorrectInputMessage;
    }

    public function getOptions(): array
    {
        return array_merge($this->getLimitOptions(), [
            'incorrectInputMessage' => [
                'template' => $this->getIncorrectInputMessage(),
                'parameters' => [],
            ],
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ]);
    }

    public function getHandler(): string
    {
        return CountHandler::class;
    }
}
