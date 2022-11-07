<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use RuntimeException;
use Yiisoft\Validator\ValidationContext;

/**
 * Validates if the specified value is greater than or equal to another value or attribute.
 *
 * The value being validated with {@see GreaterThanOrEqual::$targetValue} or {@see GreaterThanOrEqual::$targetAttribute}, which
 * is set in the constructor.
 *
 * The default validation function is based on string values, which means the values
 * are checked byte by byte. When validating numbers, make sure to change {@see GreaterThanOrEqual::$type} to
 * {@see GreaterThanOrEqual::TYPE_NUMBER} to enable numeric validation.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class GreaterThanOrEqual extends Compare
{
    public function __construct(
        /**
         * @var scalar|null The constant value to be greater than or equal to. When both this property and
         * {@see $targetAttribute} are set, this property takes precedence.
         */
        private int|float|string|bool|null $targetValue = null,
        /**
         * @var string|null The attribute to be greater than or equal to. When both this property and
         * {@see $targetValue} are set, the {@see $targetValue} takes precedence.
         */
        private string|null $targetAttribute = null,
        /**
         * @var string|null $message User-defined error message.
         */
        private string|null $message = null,
        /**
         * @var string $type The type of the values being validated.
         */
        private string $type = self::TYPE_STRING,
        bool|callable|null $skipOnEmpty = false,
        private bool $skipOnError = false,
        /**
         * @var Closure(mixed, ValidationContext):bool|null
         */
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
            operator: '>=',
            skipOnEmpty: $skipOnEmpty,
            skipOnError: $this->skipOnError,
            when: $this->when
        );
    }

    public function getName(): string
    {
        return 'greaterThanOrEqual';
    }
}
