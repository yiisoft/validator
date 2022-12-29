<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use Countable;
use Yiisoft\Validator\AfterInitAttributeEventInterface;
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
 * Defines validation options to check that the value contains certain number of items.
 * Can be applied to arrays or classes implementing {@see Countable} interface.
 *
 * @see CountHandler
 *
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Count implements
    RuleWithOptionsInterface,
    SkipOnErrorInterface,
    WhenInterface,
    SkipOnEmptyInterface,
    LimitInterface,
    AfterInitAttributeEventInterface
{
    use LimitTrait;
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @var object|null Object being validated.
     */
    private ?object $objectValidated = null;

    /**
     * @param int|null $min Minimum number of items. null means no minimum number limit. Can't be combined with
     * {@see $exactly}. See {@see $lessThanMinMessage} for the customized message for a value with too few items.
     * @param int|null $max Maximum number of items. null means no maximum number limit. Can't be combined with
     * {@see $exactly}. See {@see $greaterThanMaxMessage} for the customized message for a value with too many items.
     * @param int|null $exactly Exact number of items. `null` means no strict comparison. Mutually exclusive with
     * {@see $min} and {@see $max}.
     * @param string $incorrectInputMessage Error message used when the value is neither an array nor an object
     * implementing {@see \Countable} interface.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{type}`: the type of the value being validated.
     * @param string $lessThanMinMessage Error message used when the number of items is smaller than {@see $min}.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{min}`: minimum number of items required.
     * - `{number}`: actual number of items.
     * @param string $greaterThanMaxMessage Error message used when the number of items is greater than {@see $max}.
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
        private string $incorrectInputMessage = 'This value must be an array or implement \Countable interface.',
        string $lessThanMinMessage = 'This value must contain at least {min, number} {min, plural, one{item} ' .
        'other{items}}.',
        string $greaterThanMaxMessage = 'This value must contain at most {max, number} {max, plural, one{item} ' .
        'other{items}}.',
        string $notExactlyMessage = 'This value must contain exactly {exactly, number} {exactly, plural, one{item} ' .
        'other{items}}.',
        private mixed $skipOnEmpty = null,
        private bool $skipOnError = false,
        private Closure|null $when = null,
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
        return 'count';
    }

    /**
     * Get error message used when the value is neither an array nor a class implementing {@see \Countable} interface.
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
     * Get object being validated.
     *
     * @return object|null Object being validated.
     *
     * @see $objectValidated
     */
    public function getObjectValidated(): ?object
    {
        return $this->objectValidated;
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

    public function afterInitAttribute(object $object, int $target): void
    {
        if ($target === Attribute::TARGET_CLASS) {
            $this->objectValidated = $object;
        }
    }
}
