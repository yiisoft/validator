<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Closure;
use DateTimeInterface;
use InvalidArgumentException;
use Stringable;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\RuleWithOptionsInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\WhenInterface;

use function in_array;

/**
 * Abstract base for all the comparison validation rules.
 *
 * The validated value is compared with {@see AbstractCompare::$targetValue} or
 * {@see AbstractCompare::$targetAttribute} value of validated data set.
 *
 * The default comparison is based on number values (including float values). It's also possible to compare values as
 * strings byte by byte and compare original values as is. See {@see AbstractCompare::$type} for all possible options.
 *
 * It supports different comparison operators, specified via the {@see AbstractCompare::$operator}.
 *
 * @see CompareHandler
 *
 * @psalm-import-type WhenType from WhenInterface
 */
abstract class AbstractCompare implements
    RuleWithOptionsInterface,
    SkipOnEmptyInterface,
    SkipOnErrorInterface,
    WhenInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * A default for {@see $incorrectInputMessage}.
     */
    protected const DEFAULT_INCORRECT_INPUT_MESSAGE = 'The allowed types are integer, float, string, boolean, null ' .
    'and object implementing \Stringable interface or \DateTimeInterface.';
    /**
     * A default for {@see $incorrectDataSetTypeMessage}.
     */
    protected const DEFAULT_INCORRECT_DATA_SET_TYPE_MESSAGE = 'The attribute value returned from a custom data set ' .
    'must have one of the following types: integer, float, string, boolean, null or an object implementing ' .
    '\Stringable interface or \DateTimeInterface.';
    /**
     * List of valid types.
     *
     * @see CompareType
     */
    private const VALID_TYPES = [CompareType::ORIGINAL, CompareType::STRING, CompareType::NUMBER];
    /**
     * Map of valid operators. It's used instead of a list for better performance.
     */
    private const VALID_OPERATORS_MAP = [
        '==' => 1,
        '===' => 1,
        '!=' => 1,
        '!==' => 1,
        '>' => 1,
        '>=' => 1,
        '<' => 1,
        '<=' => 1,
    ];

    /**
     * @param mixed $targetValue The value to be compared with. When both this property and {@see $targetAttribute} are
     * set, this property takes precedence.
     * @param string|null $targetAttribute The name of the attribute to be compared with. When both this property and
     * {@see $targetValue} are set, the {@see $targetValue} takes precedence.
     * @param string $incorrectInputMessage A message used when the input is incorrect.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{type}`: the type of the value being validated.
     * @param string $incorrectDataSetTypeMessage A message used when the value returned from a custom
     * data set is neither scalar nor null.
     *
     * You may use the following placeholders in the message:
     *
     * - `{type}`: type of the value.
     * @param string|null $message A message used when the value is not valid.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{targetValue}`: the value to be compared with.
     * - `{targetAttribute}`: the name of the attribute to be compared with.
     * - `{targetAttributeValue}`: the value extracted from the attribute to be compared with if this attribute was set.
     * - `{targetValueOrAttribute}`: the value to be compared with or, if it's absent, the name of the attribute to be
     * compared with.
     * - `{value}`: the value being validated.
     *
     * When {@see CompareType::ORIGINAL} is used with complex types (neither scalar nor `null`), `{targetValue}`,
     * `{targetAttributeValue}` and `{targetValueOrAttribute}` parameters might contain the actual type instead of the
     * value, e.g. "object" for predictable formatting.
     * @param string $type The type of the values being compared:
     *
     * - {@see CompareType::NUMBER} - default, both values will be converted to float numbers before comparison.
     * - {@see CompareType::ORIGINAL} - compare the values as is.
     * - {@see CompareType::STRING} - cast both values to strings before comparison.
     *
     * {@see CompareType::NUMBER} and {@see CompareType::STRING} allow only scalar and `null` values, also objects
     * implementing {@see Stringable} interface or {@see DateTimeInterface} (validated values must be in Unix Timestamp
     * format).
     *
     * {@see CompareType::ORIGINAL} allows any values. All PHP comparison rules apply here, see comparison operators -
     * {@see https://www.php.net/manual/en/language.operators.comparison.php} and PHP type comparison tables -
     * {@see https://www.php.net/manual/en/types.comparisons.php} sections in official PHP documentation.
     *
     * @psalm-param CompareType::ORIGINAL | CompareType::STRING | CompareType::NUMBER $type
     *
     * @param string $operator The operator for comparison. The following operators are supported:
     *
     * - `==`: check if two values are equal. The comparison is done in non-strict mode.
     * - `===`: check if two values are equal. The comparison is done in strict mode.
     * - `!=`: check if two values are NOT equal. The comparison is done in non-strict mode.
     * - `!==`: check if two values are NOT equal. The comparison is done in strict mode.
     * - `>`: check if value being validated is greater than the value being compared with.
     * - `>=`: check if value being validated is greater than or equal to the value being compared with.
     * - `<`: check if value being validated is less than the value being compared with.
     * - `<=`: check if value being validated is less than or equal to the value being compared with.
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
        private mixed $targetValue = null,
        private string|null $targetAttribute = null,
        private string $incorrectInputMessage = self::DEFAULT_INCORRECT_INPUT_MESSAGE,
        private string $incorrectDataSetTypeMessage = self::DEFAULT_INCORRECT_DATA_SET_TYPE_MESSAGE,
        private string|null $message = null,
        private string $type = CompareType::NUMBER,
        private string $operator = '==',
        private mixed $skipOnEmpty = null,
        private bool $skipOnError = false,
        private Closure|null $when = null,
    ) {
        if (!in_array($this->type, self::VALID_TYPES)) {
            $validTypesString = $this->getQuotedList(self::VALID_TYPES);
            $message = "Type \"$this->type\" is not supported. The valid types are: $validTypesString.";

            throw new InvalidArgumentException($message);
        }

        if (!isset(self::VALID_OPERATORS_MAP[$this->operator])) {
            $validOperators = array_keys(self::VALID_OPERATORS_MAP);
            $validOperatorsString = $this->getQuotedList($validOperators);
            $message = "Operator \"$operator\" is not supported. The valid operators are: $validOperatorsString.";

            throw new InvalidArgumentException($message);
        }
    }

    /**
     * Get value to be compared with.
     *
     * @return mixed Value to be compared with or `null` if it was not configured.
     *
     * @see $targetValue
     */
    public function getTargetValue(): mixed
    {
        return $this->targetValue;
    }

    /**
     * Get the name of the attribute to be compared with.
     *
     * @return string|null Name of the attribute to be compared with or `null` if it was not configured.
     *
     * @see $targetAttribute
     */
    public function getTargetAttribute(): string|null
    {
        return $this->targetAttribute;
    }

    /**
     * Get the type of the values being compared.
     *
     * @return string The type of the values being compared. Either {@see CompareType::STRING}
     * or {@see CompareType::NUMBER}.
     *
     * @psalm-return CompareType::ORIGINAL | CompareType::STRING | CompareType::NUMBER $type
     *
     * @see $type
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the operator for comparison.
     *
     * @return string The operator for comparison.
     *
     * @see $operator
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * Get message used when the input is incorrect.
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
     * Get message used when the value returned from a custom
     * data set s not scalar.
     *
     * @return string Error message.
     *
     * @see $incorrectDataSetTypeMessage
     */
    public function getIncorrectDataSetTypeMessage(): string
    {
        return $this->incorrectDataSetTypeMessage;
    }

    /**
     * Get a message used when the value is not valid.
     *
     * @return string Error message.
     *
     * @see $message
     */
    public function getMessage(): string
    {
        return $this->message ?? match ($this->operator) {
            '==', => 'Value must be equal to "{targetValueOrAttribute}".',
            '===' => 'Value must be strictly equal to "{targetValueOrAttribute}".',
            '!=' => 'Value must not be equal to "{targetValueOrAttribute}".',
            '!==' => 'Value must not be strictly equal to "{targetValueOrAttribute}".',
            '>' => 'Value must be greater than "{targetValueOrAttribute}".',
            '>=' => 'Value must be greater than or equal to "{targetValueOrAttribute}".',
            '<' => 'Value must be less than "{targetValueOrAttribute}".',
            '<=' => 'Value must be less than or equal to "{targetValueOrAttribute}".',
        };
    }

    public function getOptions(): array
    {
        $isTargetValueSimple = $this->targetValue === null || is_scalar($this->targetValue);

        if (!$isTargetValueSimple) {
            $messageParameters = ['targetAttribute' => $this->targetAttribute];
        } else {
            $messageParameters = [
                'targetValue' => $this->targetValue,
                'targetAttribute' => $this->targetAttribute,
                'targetValueOrAttribute' => $this->targetAttribute ?? $this->targetValue,
            ];
        }

        $options = [
            'targetAttribute' => $this->targetAttribute,
            'incorrectInputMessage' => [
                'template' => $this->incorrectInputMessage,
                'parameters' => $messageParameters,
            ],
            'incorrectDataSetTypeMessage' => [
                'template' => $this->incorrectDataSetTypeMessage,
                'parameters' => $messageParameters,
            ],
            'message' => [
                'template' => $this->getMessage(),
                'parameters' => $messageParameters,
            ],
            'type' => $this->type,
            'operator' => $this->operator,
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ];
        if (!$isTargetValueSimple) {
            return $options;
        }

        return array_merge(['targetValue' => $this->targetValue], $options);
    }

    public function getHandler(): string
    {
        return CompareHandler::class;
    }

    /**
     * Formats list of strings as a single string where items separated by the comma and each item is wrapped with
     * double quotes.
     *
     * For example, for `['item1', 'item2']` list, the output will be `"item1", "item2"`.
     *
     * @param string[] $items Initial list of strings to format.
     *
     * @return string Resulting formatted string.
     */
    private function getQuotedList(array $items): string
    {
        return '"' . implode('", "', $items) . '"';
    }
}
