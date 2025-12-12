<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use BackedEnum;
use Closure;
use InvalidArgumentException;
use UnitEnum;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\DumpedRuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\WhenInterface;

/**
 * Defines validation options to check that the value is one of the values (or names) contained in an enum of the
 * specified class.
 * If the {@see In::$not} is set, the validation logic is inverted and the rule will ensure that the value
 * is NOT one of them.
 *
 * @see InEnumHandler
 *
 * @psalm-import-type SkipOnEmptyValue from SkipOnEmptyInterface
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class InEnum implements DumpedRuleInterface, SkipOnErrorInterface, WhenInterface, SkipOnEmptyInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @param string $class Class of the enum to user.
     * @param bool $useNames Whether to use names for backed enums instead of value.
     * @param bool $strict Whether the comparison to each value in the set is strict:
     *
     * - Strict mode meaning the type and the value must both match.
     * - Non-strict mode meaning that type juggling is performed first before the comparison. You can
     * read more in the PHP docs:
     *
     * - {@link https://www.php.net/manual/en/language.operators.comparison.php}
     * - {@link https://www.php.net/manual/en/types.comparisons.php}
     * - {@link https://www.php.net/manual/en/language.types.type-juggling.php}
     *
     * Defaults to `false` meaning non-strict mode is used.
     * @param bool $not Whether to invert the validation logic. Defaults to `false`. If set to `true`, the value must NOT
     * be among the list of {@see $values}.
     * @param string $message Error message when the value is not in a set of value.
     *
     * You may use the following placeholders in the message:
     *
     * - `{property}`: the name of the attribute.
     * - `{Property}`: the capitalized name of the attribute.
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
        private string $class,
        private bool $useNames = false,
        private bool $strict = false,
        private bool $not = false,
        private string $message = '{Property} is not in the list of acceptable values.',
        bool|callable|null $skipOnEmpty = null,
        private bool $skipOnError = false,
        private ?Closure $when = null,
    ) {
        $this->skipOnEmpty = $skipOnEmpty;

        if (!is_subclass_of($this->class, UnitEnum::class)) {
            throw new InvalidArgumentException(
                sprintf('Class should be an enum class string, %s provided.', get_debug_type($this->class)),
            );
        }
    }

    public function getName(): string
    {
        return 'inEnum';
    }

    /**
     * Get a set of values to check against.
     *
     * @return array A set of values.
     */
    public function getValues(): array
    {
        if (is_subclass_of($this->class, BackedEnum::class) && !$this->useNames) {
            return array_column($this->class::cases(), 'value');
        }

        /**
         * @psalm-suppress InvalidStringClass
         * @psalm-var array<array-key, mixed> $cases
         */
        $cases = $this->class::cases();

        return array_column($cases, 'name');
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
     * Get error message when the value is not in a set of {@see $values}.
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
            'values' => $this->getValues(),
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
        return InEnumHandler::class;
    }
}
