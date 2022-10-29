<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use RuntimeException;
use Yiisoft\Validator\ValidationContext;

/**
 * Validates if the specified value is less than another value or attribute.
 *
 * The value being validated with {@see LessThan::$targetValue} or {@see LessThan::$targetAttribute}, which
 * is set in the constructor.
 *
 * The default validation function is based on string values, which means the values
 * are checked byte by byte. When validating numbers, make sure to change {@see LessThan::$type} to
 * {@see LessThan::TYPE_NUMBER} to enable numeric validation.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class LessThan extends Compare
{
    /**
     * @param mixed $targetValue The constant value to be less than. When both this property and
     * {@see $targetAttribute} are set, this property takes precedence.
     * @param string|null $targetAttribute The attribute to be less than. When both this property and
     * {@see $targetValue} are set, the {@see $targetValue} takes precedence.
     * @param string|null $message User-defined error message.
     * @param string $type The type of the values being validated.
     * @param bool|callable|null $skipOnEmpty
     * @param bool $skipOnError
     * @param Closure(mixed, ValidationContext):bool|null $when
     */
    public function __construct(
        private mixed $targetValue = null,
        private string|null $targetAttribute = null,
        private string|null $message = null,
        private string $type = self::TYPE_STRING,
        mixed $skipOnEmpty = false,
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
            operator: '<',
            skipOnEmpty: $skipOnEmpty,
            skipOnError: $this->skipOnError,
            when: $this->when
        );
    }

    public function getName(): string
    {
        return 'lessThan';
    }
}
