<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use InvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;
use RuntimeException;
use Yiisoft\Validator\ParametrizedRuleInterface;
use Yiisoft\Validator\BeforeValidationInterface;
use Yiisoft\Validator\Rule\Trait\HandlerClassNameTrait;
use Yiisoft\Validator\Rule\Trait\BeforeValidationTrait;
use Yiisoft\Validator\Rule\Trait\RuleNameTrait;
use Yiisoft\Validator\ValidationContext;

/**
 * Compares the specified value with another value.
 *
 * The value being compared with a constant {@see CompareTo::$compareValue}, which is set
 * in the constructor.
 *
 * It supports different comparison operators, specified
 * via the {@see CompareTo::$operator}.
 *
 * The default comparison function is based on string values, which means the values
 * are compared byte by byte. When comparing numbers, make sure to change {@see CompareTo::$type} to
 * {@see CompareTo::TYPE_NUMBER} to enable numeric comparison.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class CompareTo implements ParametrizedRuleInterface, BeforeValidationInterface
{
    use BeforeValidationTrait;
    use HandlerClassNameTrait;
    use RuleNameTrait;

    /**
     * Constant for specifying the comparison as string values.
     * No conversion will be done before comparison.
     *
     * @see CompareTo::$type
     */
    public const TYPE_STRING = 'string';
    /**
     * Constant for specifying the comparison as numeric values.
     * String values will be converted into numbers before comparison.
     *
     * @see CompareTo::$type
     */
    public const TYPE_NUMBER = 'number';

    private array $validOperators = [
        '==' => 1,
        '===' => 1,
        '!=' => 1,
        '!==' => 1,
        '>' => 1,
        '>=' => 1,
        '<' => 1,
        '<=' => 1,
    ];

    public function __construct(
        /**
         * @var mixed the constant value to be compared with. When both this property
         * and {@see $compareAttribute} are set, this property takes precedence.
         */
        private $compareValue = null,
        /**
         * @var string|null the name of the attribute to be compared with. When both this property
         * and {@see $compareValue} are set, the latter takes precedence. If neither is set,
         * it assumes the comparison is against another attribute whose name is formed by
         * appending '_repeat' to the attribute being validated. For example, if 'password' is
         * being validated, then the attribute to be compared would be 'password_repeat'.
         * @see $compareValue
         */
        private ?string $compareAttribute = null,
        /**
         * @var string|null user-defined error message
         */
        private ?string $message = null,
        /**
         * @var string the type of the values being compared.
         */
        private string $type = self::TYPE_STRING,
        /**
         * @var string the operator for comparison. The following operators are supported:
         *
         * - `==`: check if two values are equal. The comparison is done is non-strict mode.
         * - `===`: check if two values are equal. The comparison is done is strict mode.
         * - `!=`: check if two values are NOT equal. The comparison is done is non-strict mode.
         * - `!==`: check if two values are NOT equal. The comparison is done is strict mode.
         * - `>`: check if value being validated is greater than the value being compared with.
         * - `>=`: check if value being validated is greater than or equal to the value being compared with.
         * - `<`: check if value being validated is less than the value being compared with.
         * - `<=`: check if value being validated is less than or equal to the value being compared with.
         *
         * When you want to compare numbers, make sure to also change @see CompareTo::$type} to
         * {@see CompareTo::TYPE_NUMBER}.
         */
        private string $operator = '==',
        private bool $skipOnEmpty = false,
        private bool $skipOnError = false,
        /**
         * @var Closure(mixed, ValidationContext):bool|null
         */
        private ?Closure $when = null,
    ) {
        if (!isset($this->validOperators[$operator])) {
            throw new InvalidArgumentException("Operator \"$operator\" is not supported.");
        }
    }

    /**
     * @return mixed
     */
    public function getCompareValue(): mixed
    {
        return $this->compareValue;
    }

    /**
     * @return string|null
     */
    public function getCompareAttribute(): ?string
    {
        return $this->compareAttribute;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    public function getMessage(): string
    {
        return $this->message ?? match ($this->operator) {
            '==', '===' => 'Value must be equal to "{compareValueOrAttribute}".',
            '!=', '!==' => 'Value must not be equal to "{compareValueOrAttribute}".',
            '>' => 'Value must be greater than "{compareValueOrAttribute}".',
            '>=' => 'Value must be greater than or equal to "{compareValueOrAttribute}".',
            '<' => 'Value must be less than "{compareValueOrAttribute}".',
            '<=' => 'Value must be less than or equal to "{compareValueOrAttribute}".',
            default => throw new RuntimeException("Unknown operator: {$this->operator}"),
        };
    }

    #[ArrayShape([
        'compareValue' => 'mixed',
        'compareAttribute' => '',
        'message' => 'array',
        'type' => 'string',
        'operator' => 'string',
        'skipOnEmpty' => 'bool',
        'skipOnError' => 'bool',
    ])]
    public function getOptions(): array
    {
        return [
            'compareValue' => $this->compareValue,
            'compareAttribute' => $this->compareAttribute,
            'message' => [
                'message' => $this->getMessage(),
                'parameters' => [
                    'compareValue' => $this->compareValue,
                    'compareAttribute' => $this->compareAttribute,
                    'compareValueOrAttribute' => $this->compareValue ?? $this->compareAttribute,
                ],
            ],
            'type' => $this->type,
            'operator' => $this->operator,
            'skipOnEmpty' => $this->skipOnEmpty,
            'skipOnError' => $this->skipOnError,
        ];
    }
}
