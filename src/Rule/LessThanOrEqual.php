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
 * Checks if the specified value is less than another value or attribute.
 *
 * The value being checked with a constant {@see LessThan::$targetValue} or attribute {@see LessThan::$targetAttribute}, which
 * is set in the constructor.
 *
 * The default equality function is based on string values, which means the values
 * are equals byte by byte. When comparing numbers, make sure to change {@see LessThan::$type} to
 * {@see LessThan::TYPE_NUMBER} to enable numeric comparison.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class LessThanOrEqual implements ParametrizedRuleInterface, BeforeValidationInterface
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
         * @var mixed the constant value to be equal with. When both this property
         * and {@see $targetAttribute} are set, this property takes precedence.
         */
        private $targetValue = null,
        /**
         * @var mixed the constant value to be compared with. When both this property
         * and {@see $targetValue} are set, the previous one takes precedence.
         */
        private ?string $targetAttribute = null,
        /**
         * @var string|null user-defined error message
         */
        private ?string $message = null,
        /**
         * @var string the type of the values being compared.
         */
        private string $type = self::TYPE_STRING,
        /**
         * @var bool Whether this validator strictly check.
         */
        private bool $strict = false,
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
        return $this->message ?? 'Value must be equal to "{targetValueOrAttribute}".';
    }

    #[ArrayShape([
        'targetValue' => 'mixed',
        'targetAttribute' => 'string|null',
        'message' => 'array',
        'type' => 'string',
        'strict' => 'bool',
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
            'strict' => $this->strict,
            'skipOnEmpty' => $this->skipOnEmpty,
            'skipOnError' => $this->skipOnError,
        ];
    }
}
