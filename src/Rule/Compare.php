<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Closure;
use InvalidArgumentException;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\WhenInterface;

abstract class Compare implements SerializableRuleInterface, SkipOnEmptyInterface, SkipOnErrorInterface, WhenInterface
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
        private string $incorrectDataSetTypeMessage = 'The attribute value returned from a custom data set must have ' .
        'a scalar type.',
        private string|null $nonScalarMessage = null,
        /**
         * @var string|null User-defined error message.
         */
        private string|null $scalarMessage = null,
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
            throw new InvalidArgumentException("Operator \"$operator\" is not supported.");
        }
    }

    /**
     * @return scalar|null
     */
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

    public function getIncorrectDataSetTypeMessage(): string
    {
        return $this->incorrectDataSetTypeMessage;
    }

    public function getNonScalarMessage(): string
    {
        return $this->nonScalarMessage ?? match ($this->operator) {
            '==', '===' => 'The non-scalar value must be equal to "{targetValueOrAttribute}".',
            '!=', '!==' => 'The non-scalar value must not be equal to "{targetValueOrAttribute}".',
            '>' => 'The non-scalar value must be greater than "{targetValueOrAttribute}".',
            '>=' => 'The non-scalar value must be greater than or equal to "{targetValueOrAttribute}".',
            '<' => 'The non-scalar value must be less than "{targetValueOrAttribute}".',
            '<=' => 'The non-scalar value must be less than or equal to "{targetValueOrAttribute}".',
        };
    }

    public function getScalarMessage(): string
    {
        return $this->scalarMessage ?? match ($this->operator) {
            '==', '===' => 'The scalar value must be equal to "{targetValueOrAttribute}".',
            '!=', '!==' => 'The scalar value must not be equal to "{targetValueOrAttribute}".',
            '>' => 'The scalar value must be greater than "{targetValueOrAttribute}".',
            '>=' => 'The scalar value must be greater than or equal to "{targetValueOrAttribute}".',
            '<' => 'The scalar value must be less than "{targetValueOrAttribute}".',
            '<=' => 'The scalar value must be less than or equal to "{targetValueOrAttribute}".',
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
            'incorrectDataSetTypeMessage' => [
                'message' => $this->incorrectDataSetTypeMessage,
            ],
            'nonScalarMessage' => [
                'message' => $this->getNonScalarMessage(),
                'parameters' => $messageParameters,
            ],
            'scalarMessage' => [
                'message' => $this->getScalarMessage(),
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
