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
 * Defines validation options to check that the value is one of the values provided in {@see $values}.
 * If the {@see In::$not} is set, the validation logic is inverted and the rule will ensure that the value
 * is NOT one of them.
 *
 * @see InHandler
 *
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class In implements RuleWithOptionsInterface, SkipOnErrorInterface, WhenInterface, SkipOnEmptyInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @param iterable $values A set of values to check against.
     * @psalm-param iterable<scalar> $values
     * @param bool $strict Whether the comparison is strict (both type and value must be the same).
     * @param bool $not Whether to invert the validation logic. Defaults to `false`. If set to `true`, the value must NOT
     * be among the list of {@see $values}.
     * @param string $message Error message when the value is not in a set of value.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the name of the attribute.
     *
     * @param bool|callable|null $skipOnEmpty Whether to skip this rule if the value validated is empty.
     * See {@see SkipOnEmptyInterface}.
     * @param bool $skipOnError Whether to skip this rule if any of the previous rules gave an error.
     * See {@see SkipOnErrorInterface}.
     * @param Closure|null $when A callable to define a condition for applying the rule.
     * See {@see WhenInterface}.
     * @psalm-param WhenType $when
     */
    public function __construct(
        private iterable $values,
        private bool $strict = false,
        private bool $not = false,
        private string $message = 'This value is invalid.',
        private mixed $skipOnEmpty = null,
        private bool $skipOnError = false,
        private Closure|null $when = null,
    ) {
    }

    public function getName(): string
    {
        return 'inRange';
    }

    /**
     * Get a set of values to check against.
     *
     * @return iterable A set of values.
     */
    public function getValues(): iterable
    {
        return $this->values;
    }

    /**
     * Whether the comparison is strict (both type and value must be the same).
     *
     * @return bool Whether the comparison is strict.
     */
    public function isStrict(): bool
    {
        return $this->strict;
    }

    /**
     * Whether to invert the validation logic. Defaults to `false`. If set to `true`, the value must NOT
     * be among the list of {@see $values}.
     *
     * @return bool Whether to invert the validation logic.
     */
    public function isNot(): bool
    {
        return $this->not;
    }

    /**
     * Get error message when the value is not in a set of value.
     *
     * @return string Error message.
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    public function getOptions(): array
    {
        return [
            'values' => $this->values,
            'strict' => $this->strict,
            'not' => $this->not,
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
        return InHandler::class;
    }
}
