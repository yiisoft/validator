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

    /**
     * @param mixed $targetValue The constant value to be compared with. When both this property and
     * {@see $targetAttribute} are set, this property takes precedence.
     * @param string|null $targetAttribute The name of the attribute to be compared with. When both this property and
     * {@see $targetValue} are set, the {@see $targetValue} takes precedence.
     * @param string|null $message User-defined error message.
     * @param string $type The type of the values being compared.
     * @param string $operator The operator for comparison. The following operators are supported:
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
     * When you want to compare numbers, make sure to also change @see $type} to {@see TYPE_NUMBER}.
     * @param bool|callable|null $skipOnEmpty
     * @param bool $skipOnError
     * @param Closure(mixed, ValidationContext):bool|null $when
     */
    public function __construct(
        private mixed $targetValue = null,
        private string|null $targetAttribute = null,
        private string|null $message = null,
        private string $type = self::TYPE_STRING,
        private string $operator = '==',
        private mixed $skipOnEmpty = null,
        private bool $skipOnError = false,
        private ?Closure $when = null,
    ) {
        if (!isset($this->validOperatorsMap[$this->operator])) {
            throw new InvalidArgumentException("Operator \"$operator\" is not supported.");
        }
    }

    public function getTargetValue(): mixed
    {
        return $this->targetValue;
    }

    public function getTargetAttribute(): ?string
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
        return [
            'targetValue' => $this->targetValue,
            'targetAttribute' => $this->targetAttribute,
            'message' => [
                'message' => $this->getMessage(),
                'parameters' => [
                    'targetValue' => $this->targetValue,
                    'targetAttribute' => $this->targetAttribute,
                    'targetValueOrAttribute' => $this->targetValue ?? $this->targetAttribute,
                ],
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
