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
 * Validates if the specified value is greater than or equal to another value or attribute.
 *
 * The value being validated with {@see GreaterThanOrEqual::$targetValue} or {@see GreaterThanOrEqual::$targetAttribute}, which
 * is set in the constructor.
 *
 * The default validation function is based on string values, which means the values
 * are equals byte by byte. When validating numbers, make sure to change {@see GreaterThanOrEqual::$type} to
 * {@see GreaterThanOrEqual::TYPE_NUMBER} to enable numeric validation.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class GreaterThanOrEqual implements ParametrizedRuleInterface, BeforeValidationInterface
{
    use BeforeValidationTrait;
    use HandlerClassNameTrait;
    use RuleNameTrait;

    /**
     * Constant for specifying validation as string values.
     * No conversion will be done before validation.
     *
     * @see $type
     */
    public const TYPE_STRING = 'string';
    /**
     * Constant for specifying validation as numeric values.
     * String values will be converted into numbers before validation.
     *
     * @see $type
     */
    public const TYPE_NUMBER = 'number';

    public function __construct(
        /**
         * @var mixed the constant value to be greater than or equal to. When both this property
         * and {@see $targetAttribute} are set, this property takes precedence.
         */
        private $targetValue = null,
        /**
         * @var string|null the attribute to be greater than or equal to. When both this property
         * and {@see $targetValue} are set, the {@see $targetValue} takes precedence.
         */
        private ?string $targetAttribute = null,
        /**
         * @var string|null user-defined error message
         */
        private ?string $message = null,
        /**
         * @var string the type of the values being validated.
         */
        private string $type = self::TYPE_STRING,
        private bool $skipOnEmpty = false,
        private bool $skipOnError = false,
        /**
         * @var Closure(mixed, ValidationContext):bool|null
         */
        private ?Closure $when = null,
    ) {
        if ($this->targetValue === null && $this->targetAttribute === null) {
            throw new RuntimeException('Either "targetValue" or "targetAttribute" must be specified.');
        }
    }

    /**
     * @return mixed
     */
    public function getTargetValue(): mixed
    {
        return $this->targetValue;
    }

    /**
     * @return string|null
     */
    public function getTargetAttribute(): ?string
    {
        return $this->targetAttribute;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    public function getMessage(): string
    {
        return $this->message ?? 'Value must be greater than or equal to "{targetValueOrAttribute}".';
    }

    #[ArrayShape([
        'targetValue' => 'mixed',
        'targetAttribute' => 'string|null',
        'message' => 'array',
        'type' => 'string',
        'skipOnEmpty' => 'bool',
        'skipOnError' => 'bool',
    ])]
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
            'skipOnEmpty' => $this->skipOnEmpty,
            'skipOnError' => $this->skipOnError,
        ];
    }
}
