<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use Yiisoft\Validator\DumpedRuleInterface;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\WhenInterface;

/**
 * A variation of {@see In} rule allowing to use the set of values instead of single value as an input for checking if
 * it's a subset of the set provided in {@see Subset::$values}.
 *
 * The order of items in the validated set is not important.
 *
 * Nested arrays are supported in both {@see $values} argument and in the validated value (the order of values in lists
 * must match, the order of keys in associative arrays is not important).
 *
 * @see SubsetHandler Corresponding handler performing the actual validation.
 *
 * @psalm-import-type SkipOnEmptyValue from SkipOnEmptyInterface
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Subset implements DumpedRuleInterface, SkipOnErrorInterface, WhenInterface, SkipOnEmptyInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @param iterable $values A set of values to check against. Nested arrays are supported too (the order of values in
     * lists must match, the order of keys in associative arrays is not important).
     * @param bool $strict Whether the comparison for each value in the set is strict:
     *
     * - Strict mode uses `===` operator meaning the type and the value must both match.
     * - Non-strict mode uses `==` operator meaning that type juggling is performed first before the comparison. You can
     * read more in the PHP docs:
     *
     * - {@link https://www.php.net/manual/en/language.operators.comparison.php}
     * - {@link https://www.php.net/manual/en/types.comparisons.php}
     * - {@link https://www.php.net/manual/en/language.types.type-juggling.php}
     *
     * Defaults to `false` meaning non-strict mode is used.
     * @param string $incorrectInputMessage Error message used when validation fails because the validated value is not
     * an iterable.
     *
     * You may use the following placeholders in the message:
     *
     * - `{property}`: the translated label of the property being validated.
     * - `{type}`: the type of the value being validated.
     * @param string $message Error message used when validation fails because the validated value is not a subset of
     * the set provided in {@see $values}.
     *
     * You may use the following placeholders in the message:
     *
     * - `{property}`: the translated label of the property being validated.
     * @param bool|callable|null $skipOnEmpty Whether to skip this rule if the validated value is empty / not passed.
     * See {@see SkipOnEmptyInterface}.
     * @param bool $skipOnError Whether to skip this rule if any of the previous rules gave an error. See
     * {@see SkipOnErrorInterface}.
     * @param Closure|null $when A callable to define a condition for applying the rule. See {@see WhenInterface}.
     *
     * @psalm-param SkipOnEmptyValue $skipOnEmpty
     * @psalm-param WhenType $when
     */
    public function __construct(
        private iterable $values,
        private bool $strict = false,
        private string $incorrectInputMessage = '{Property} must be iterable. {type} given.',
        private string $message = '{Property} is not a subset of acceptable values.',
        bool|callable|null $skipOnEmpty = null,
        private bool $skipOnError = false,
        private Closure|null $when = null,
    ) {
        $this->skipOnEmpty = $skipOnEmpty;
    }

    public function getName(): string
    {
        return self::class;
    }

    /**
     * Gets a set of values to check against.
     *
     * @return iterable A set of values.
     */
    public function getValues(): iterable
    {
        return $this->values;
    }

    /**
     * Whether the comparison for each value in the set is strict.
     *
     * @return bool `true` - strict, `false` - non-strict.
     *
     * @see $strict
     */
    public function isStrict(): bool
    {
        return $this->strict;
    }

    /**
     * Gets error message used when validation fails because the type of validated value is incorrect.
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
     * Gets error message used when validation fails because the validated value is not a subset of the set provided in
     * {@see $values}.
     *
     * @return string Error message / template.
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
            'values' => $this->values,
            'strict' => $this->strict,
            'incorrectInputMessage' => [
                'template' => $this->incorrectInputMessage,
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
        return SubsetHandler::class;
    }
}
