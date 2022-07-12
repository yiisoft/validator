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
 * Compares equality the specified value with another value or attribute.
 *
 * The value being compared with a constant {@see Equal::$equalValue} or attribute {@see Equal::$equalAttribute}, which
 * is set in the constructor.
 *
 * The default equality function is based on string values, which means the values
 * are equals byte by byte. When comparing numbers, make sure to change {@see Equal::$type} to
 * {@see Equal::TYPE_NUMBER} to enable numeric comparison.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Equal implements ParametrizedRuleInterface, BeforeValidationInterface
{
    use BeforeValidationTrait;
    use HandlerClassNameTrait;
    use RuleNameTrait;

    /**
     * Constant for specifying the equality as string values.
     * No conversion will be done before equality.
     *
     * @see $type
     */
    public const TYPE_STRING = 'string';
    /**
     * Constant for specifying equality as numeric values.
     * String values will be converted into numbers before equality.
     *
     * @see $type
     */
    public const TYPE_NUMBER = 'number';

    public function __construct(
        /**
         * @var mixed the constant value to be equal with. When both this property
         * and {@see $equalAttribute} are set, this property takes precedence.
         */
        private $equalValue = null,
        /**
         * @var mixed the constant value to be compared with. When both this property
         * and {@see $equalValue} are set, the latter takes precedence.
         */
        private ?string $equalAttribute = null,
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
        if ($this->equalValue === null && $this->equalAttribute === null) {
            throw new RuntimeException('Either "value" or "attribute" must be specified.');
        }
    }

    /**
     * @return mixed
     */
    public function getEqualValue(): mixed
    {
        return $this->equalValue;
    }

    /**
     * @return string|null
     */
    public function getEqualAttribute(): ?string
    {
        return $this->equalAttribute;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    public function shouldCheckStrictly(): bool
    {
        return $this->strict;
    }

    public function getMessage(): string
    {
        return $this->message ?? 'Value must be equal to "{equalValueOrAttribute}".';
    }

    #[ArrayShape([
        'equalValue' => 'mixed',
        'equalAttribute' => 'string|null',
        'message' => 'array',
        'type' => 'string',
        'strict' => 'bool',
        'skipOnEmpty' => 'bool',
        'skipOnError' => 'bool',
    ])]
    public function getOptions(): array
    {
        return [
            'equalValue' => $this->equalValue,
            'equalAttribute' => $this->equalAttribute,
            'message' => [
                'message' => $this->getMessage(),
                'parameters' => [
                    'equalValue' => $this->equalValue,
                    'equalAttribute' => $this->equalAttribute,
                    'equalValueOrAttribute' => $this->equalValue ?? $this->equalAttribute,
                ],
            ],
            'type' => $this->type,
            'strict' => $this->strict,
            'skipOnEmpty' => $this->skipOnEmpty,
            'skipOnError' => $this->skipOnError,
        ];
    }
}
