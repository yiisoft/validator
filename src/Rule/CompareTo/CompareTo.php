<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\CompareTo;

use Attribute;
use Closure;
use InvalidArgumentException;
use RuntimeException;
use Yiisoft\Validator\ParametrizedRuleInterface;
use Yiisoft\Validator\Rule\RuleNameTrait;

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
final class CompareTo implements ParametrizedRuleInterface
{
    use RuleNameTrait;

    /**
     * Constant for specifying the comparison as string values.
     * No conversion will be done before comparison.
     *
     * @see $type
     */
    public const TYPE_STRING = 'string';
    /**
     * Constant for specifying the comparison as numeric values.
     * String values will be converted into numbers before comparison.
     *
     * @see $type
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
         * @var mixed the constant value to be compared with.
         */
        public          $compareValue,
        /**
         * @var string|null user-defined error message
         */
        public ?string $message = null,
        /**
         * @var string the type of the values being compared.
         */
        public string $type = self::TYPE_STRING,
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
         * When you want to compare numbers, make sure to also chabge @see CompareTo::$type} to
         * {@see CompareTo::TYPE_NUMBER}.
         */
        public string $operator = '==',
        public bool $skipOnEmpty = false,
        public bool $skipOnError = false,
        public ?Closure $when = null,
    ) {
        if (!isset($this->validOperators[$operator])) {
            throw new InvalidArgumentException("Operator \"$operator\" is not supported.");
        }
    }

    private function getMessage(): string
    {
        if ($this->message !== null) {
            return $this->message;
        }

        switch ($this->operator) {
            case '==':
            case '===':
                return 'Value must be equal to "{value}".';
            case '!=':
            case '!==':
                return 'Value must not be equal to "{value}".';
            case '>':
                return 'Value must be greater than "{value}".';
            case '>=':
                return 'Value must be greater than or equal to "{value}".';
            case '<':
                return 'Value must be less than "{value}".';
            case '<=':
                return 'Value must be less than or equal to "{value}".';
            default:
                throw new RuntimeException("Unknown operator: {$this->operator}");
        }
    }

    public function getOptions(): array
    {
        return [
            'compareValue' => $this->compareValue,
            'message' => [
                'message' => $this->getMessage(),
                'parameters' => ['value' => $this->compareValue],
            ],
            'type' => $this->type,
            'operator' => $this->operator,
            'skipOnEmpty' => $this->skipOnEmpty,
            'skipOnError' => $this->skipOnError,
        ];
    }
}
