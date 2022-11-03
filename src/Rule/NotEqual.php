<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use RuntimeException;
use Yiisoft\Validator\ValidationContext;

/**
 * Validates if the specified value is not equal to another value or attribute.
 *
 * The value being validated with {@see NotEqual::$targetValue} or {@see NotEqual::$targetAttribute}, which
 * is set in the constructor.
 *
 * The default validation function is based on string values, which means the values
 * are checked byte by byte. When validating numbers, make sure to change {@see NotEqual::$type} to
 * {@see NotEqual::TYPE_NUMBER} to enable numeric validation.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class NotEqual extends Compare
{
    /**
     * @param scalar|null $targetValue The constant value to not be equal to. When both this property and
     * {@see $targetAttribute} are set, this property takes precedence.
     * @param string|null $targetAttribute The constant value to not be equal to. When both this property and
     * {@see $targetValue} are set, the {@see $targetValue} takes precedence.
     * @param string|null $message User-defined error message.
     * @param string $type The type of the values being validated.
     * @param bool $strict Whether this validator strictly check.
     * @param bool|callable|null $skipOnEmpty
     * @param bool $skipOnError
     * @param Closure(mixed, ValidationContext):bool|null $when
     */
    public function __construct(
        private $targetValue = null,
        private string|null $targetAttribute = null,
        private string|null $message = null,
        private string $type = self::TYPE_STRING,
        private bool $strict = false,
        bool|callable|null $skipOnEmpty = false,
        private bool $skipOnError = false,
        private ?Closure $when = null,
    ) {
        if ($this->targetValue === null && $this->targetAttribute === null) {
            throw new RuntimeException('Either "targetValue" or "targetAttribute" must be specified.');
        }

        parent::__construct(
            targetValue: $this->targetValue,
            targetAttribute: $this->targetAttribute,
            message: $this->message,
            type: $this->type,
            operator: $this->strict ? '!==' : '!=',
            skipOnEmpty: $skipOnEmpty,
            skipOnError: $this->skipOnError,
            when: $this->when
        );
    }

    public function getName(): string
    {
        return 'notEqual';
    }
}
