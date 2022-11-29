<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Closure;
use InvalidArgumentException;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\RuleWithOptionsInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\WhenInterface;

abstract class Compare implements RuleWithOptionsInterface, SkipOnEmptyInterface, SkipOnErrorInterface, WhenInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * Constant for specifying the comparison as string values.
     * Values will be converted to strings before comparison.
     *
     * @see $type
     */
    public const TYPE_STRING = 'string';
    /**
     * Constant for specifying the comparison as numeric values.
     * Values will be converted to float numbers before comparison.
     *
     * @see $type
     */
    public const TYPE_NUMBER = 'number';

    private array $validOperatorsMap = [
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
         * @var scalar|null The constant value to be compared with. When both this property and {@see $targetAttribute}
         * are set, this property takes precedence.
         */
        private int|float|string|bool|null $targetValue = null,
        /**
         * @var string|null The name of the attribute to be compared with. When both this property and
         * {@see $targetValue} are set, the {@see $targetValue} takes precedence.
         */
        private string|null $targetAttribute = null,
        private string $incorrectInputMessage = 'The allowed types are integer, float, string, boolean and null.',
        private string $incorrectDataSetTypeMessage = 'The attribute value returned from a custom data set must have ' .
        'a scalar type.',
        /**
         * @var string|null User-defined error message.
         */
        private string|null $message = null,
        /**
         * @var string The type of the values being compared.
         */
        private string $type = self::TYPE_STRING,
        /**
         * @var string The operator for comparison. The following operators are supported:
         *
         * - `==`: check if two values are equal. The comparison is done in non-strict mode.
         * - `===`: check if two values are equal. The comparison is done in strict mode.
         * - `!=`: check if two values are NOT equal. The comparison is done in non-strict mode.
         * - `!==`: check if two values are NOT equal. The comparison is done in strict mode.
         * - `>`: check if value being validated is greater than the value being compared with.
         * - `>=`: check if value being validated is greater than or equal to the value being compared with.
         * - `<`: check if value being validated is less than the value being compared with.
         * - `<=`: check if value being validated is less than or equal to the value being compared with.
         *
         * When you want to compare numbers, make sure to also change {@see $type} to {@see TYPE_NUMBER}.
         */
        private string $operator = '==',
        /**
         * @var bool|callable|null
         */
        private mixed $skipOnEmpty = null,
        private bool $skipOnError = false,
        /**
         * @var Closure(mixed, ValidationContext):bool|null
         */
        private ?Closure $when = null,
    ) {
        if (!isset($this->validOperatorsMap[$this->operator])) {
            $wrapInQuotesCallable = static fn (string $operator): string => '"' . $operator . '"';
            /** @var string[] $validOperators */
            $validOperators = array_keys($this->validOperatorsMap);
            $validOperatorsString = implode(', ', array_map($wrapInQuotesCallable, $validOperators));
            $message = "Operator \"$operator\" is not supported. The valid operators are: $validOperatorsString.";

            throw new InvalidArgumentException($message);
        }
    }

    public function getTargetValue(): int|float|string|bool|null
    {
        return $this->targetValue;
    }

    public function getTargetAttribute(): string|null
    {
        return $this->targetAttribute;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function getIncorrectInputMessage(): string
    {
        return $this->incorrectInputMessage;
    }

    public function getIncorrectDataSetTypeMessage(): string
    {
        return $this->incorrectDataSetTypeMessage;
    }

    public function getMessage(): string
    {
        return $this->message ?? match ($this->operator) {
            '==', '===' => 'Value must be equal to "{targetValueOrAttribute}".',
            '!=', '!==' => 'Value must not be equal to "{targetValueOrAttribute}".',
            '>' => 'Value must be greater than "{targetValueOrAttribute}".',
            '>=' => 'Value must be greater than or equal to "{targetValueOrAttribute}".',
            '<' => 'Value must be less than "{targetValueOrAttribute}".',
            '<=' => 'Value must be less than or equal to "{targetValueOrAttribute}".',
        };
    }

    public function getOptions(): array
    {
        $messageParameters = [
            'targetValue' => $this->targetValue,
            'targetAttribute' => $this->targetAttribute,
            'targetValueOrAttribute' => $this->targetValue ?? $this->targetAttribute,
        ];

        return [
            'targetValue' => $this->targetValue,
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
    }

    public function getHandlerClassName(): string
    {
        return CompareHandler::class;
    }
}
