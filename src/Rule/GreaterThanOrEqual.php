<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use RuntimeException;
use Yiisoft\Validator\WhenInterface;

/**
 * Defines validation options to check that the specified value is greater than or equal to another value or attribute.
 *
 * The value being validated with {@see GreaterThanOrEqual::$targetValue} or {@see GreaterThanOrEqual::$targetAttribute},
 * which is set in the constructor.
 *
 * The default validation function is based on string values, which means the values
 * are checked byte by byte. When validating numbers, make sure to change {@see GreaterThanOrEqual::$type} to
 * {@see GreaterThanOrEqual::TYPE_NUMBER} to enable numeric validation.
 *
 *  `new GreaterThanOrEqual()` is a shortcut for `new CompareTo(operator: '>=')`.
 *
 * @see CompareHandler
 * @see Compare
 *
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class GreaterThanOrEqual extends AbstractCompare
{
    /**
     * @param scalar|null $targetValue The constant value to be greater than or equal to. When both this property and
     * {@see $targetAttribute} are set, this property takes precedence.
     * @param string|null $targetAttribute The attribute to be greater than or equal to. When both this property and
     * {@see $targetValue} are set, the {@see $targetValue} takes precedence.
     * @param string $incorrectInputMessage A message used when the input is incorrect.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{type}`: the type of the value being validated.
     * @param string $incorrectDataSetTypeMessage A message used when the value returned from a custom
     * data set s not scalar.
     *
     * You may use the following placeholders in the message:
     *
     * - `{type}`: type of the value.
     * @param string|null $message A message used when the value is not valid.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{targetValue}`: the constant value to be compared with.
     * - `{targetAttribute}`: the name of the attribute to be compared with.
     * - `{targetValueOrAttribute}`: the constant value to be compared with or, if it's absent, the name of
     *   the attribute to be compared with.
     * - `{value}`: the value of the attribute being validated.
     * @param string $type The type of the values being compared. Either {@see Compare::TYPE_STRING}
     * or {@see Compare::TYPE_NUMBER}.
     * @psalm-param Compare::TYPE_* $type
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
        private int|float|string|bool|null $targetValue = null,
        private string|null $targetAttribute = null,
        private string $incorrectInputMessage = 'The allowed types are integer, float, string, boolean and null.',
        private string $incorrectDataSetTypeMessage = 'The attribute value returned from a custom data set must have ' .
        'a scalar type.',
        private string|null $message = null,
        private string $type = self::TYPE_STRING,
        bool|callable|null $skipOnEmpty = false,
        private bool $skipOnError = false,
        private Closure|null $when = null,
    ) {
        if ($this->targetValue === null && $this->targetAttribute === null) {
            throw new RuntimeException('Either "targetValue" or "targetAttribute" must be specified.');
        }

        parent::__construct(
            targetValue: $this->targetValue,
            targetAttribute: $this->targetAttribute,
            incorrectInputMessage: $this->incorrectInputMessage,
            incorrectDataSetTypeMessage: $this->incorrectDataSetTypeMessage,
            message: $this->message,
            type: $this->type,
            operator: '>=',
            skipOnEmpty: $skipOnEmpty,
            skipOnError: $this->skipOnError,
            when: $this->when,
        );
    }

    public function getName(): string
    {
        return 'greaterThanOrEqual';
    }
}
