<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use InvalidArgumentException;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\DumpedRuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\WhenInterface;

use function count;

/**
 * Defines validation options to check that a minimum number of specified attributes are filled.
 *
 * Both arrays and objects with public properties are supported as validated values.
 *
 * @see AtLeastHandler
 *
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class AtLeast implements DumpedRuleInterface, SkipOnErrorInterface, WhenInterface, SkipOnEmptyInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @param string[] $attributes The list of required attributes that will be checked.
     * @param int $min The minimum required quantity of filled attributes to pass the validation. Defaults to 1.
     * @param string $incorrectInputMessage A message used when the input is incorrect.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{type}`: the type of the value being validated.
     * @param string $message A message used when the value is not valid.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{min}`: the minimum number of attribute values that was not met.
     * @param bool|callable|null $skipOnEmpty Whether to skip this rule if the value validated is empty.
     * See {@see SkipOnEmptyInterface}.
     * @param bool $skipOnError Whether to skip this rule if any of the previous rules gave an error.
     * See {@see SkipOnErrorInterface}.
     * @param Closure|null $when A callable to define a condition for applying the rule.
     * See {@see WhenInterface}.
     *
     * @psalm-param WhenType $when
     */
    public function __construct(
        private array $attributes,
        private int $min = 1,
        private string $incorrectInputMessage = 'The value must be an array or an object.',
        private string $message = 'At least {min, number} {min, plural, one{attribute} other{attributes}} from this ' .
        'list must be filled: {attributes}.',
        private mixed $skipOnEmpty = null,
        private bool $skipOnError = false,
        private Closure|null $when = null
    ) {
        if ($min > count($this->attributes)) {
            throw new InvalidArgumentException('$min must be no greater than amount of $attributes.');
        }
    }

    public function getName(): string
    {
        return self::class;
    }

    /**
     * Get the list of required attributes that will be checked.
     *
     * @return string[] The list of attributes.
     *
     * @see $attributes
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Get the minimum required quantity of filled attributes to pass the validation.
     *
     * @return int Minimum require quantity.
     *
     * @see $min
     */
    public function getMin(): int
    {
        return $this->min;
    }

    /**
     * Get the message used when the input is incorrect.
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
     * Get the message used when the value is not valid.
     *
     * @return string Error message.
     *
     * @see $message
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    public function getOptions(): array
    {
        return [
            'attributes' => $this->attributes,
            'min' => $this->min,
            'incorrectInputMessage' => [
                'template' => $this->incorrectInputMessage,
                'parameters' => [],
            ],
            'message' => [
                'template' => $this->message,
                'parameters' => ['min' => $this->min],
            ],
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ];
    }

    public function getHandler(): string
    {
        return AtLeastHandler::class;
    }
}
