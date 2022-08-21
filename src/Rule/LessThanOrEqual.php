<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use RuntimeException;
use Yiisoft\Validator\ValidationContext;

/**
 * Validates if the specified value is less than or equal to another value or attribute.
 *
 * The value being validated with {@see LessThanOrEqual::$targetValue} or {@see LessThanOrEqual::$targetAttribute}, which
 * is set in the constructor.
 *
 * The default validation function is based on string values, which means the values
 * are checked byte by byte. When validating numbers, make sure to change {@see LessThanOrEqual::$type} to
 * {@see LessThanOrEqual::TYPE_NUMBER} to enable numeric validation.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class LessThanOrEqual extends Compare
{
    public function __construct(
        /**
         * @var mixed The constant value to be less than or equal to. When both this property
         * and {@see $targetAttribute} are set, this property takes precedence.
         */
        private $targetValue = null,
        /**
         * @var string|null The attribute to be less than or equal to. When both this property
         * and {@see $targetValue} are set, the {@see $targetValue} takes precedence.
         */
        private ?string $targetAttribute = null,
        /**
         * @var string|null User-defined error message.
         */
        private ?string $message = null,
        /**
         * @var string The type of the values being validated.
         */
        private string $type = self::TYPE_STRING,
        private bool $skipOnEmpty = false,
        /**
         * @var callable
         */
        private $skipOnEmptyCallback = null,
        private bool $skipOnError = false,
        /**
         * @var Closure(mixed, ValidationContext):bool|null
         */
        private ?Closure $when = null,
    ) {
        $this->initSkipOnEmptyProperties($skipOnEmpty, $skipOnEmptyCallback);

        if ($this->targetValue === null && $this->targetAttribute === null) {
            throw new RuntimeException('Either "targetValue" or "targetAttribute" must be specified.');
        }
        parent::__construct(
            targetValue: $this->targetValue,
            targetAttribute: $this->targetAttribute,
            message: $this->message,
            type: $this->type,
            operator: '<=',
            skipOnEmpty: $this->skipOnEmpty,
            skipOnError: $this->skipOnError,
            when: $this->when
        );
    }
}
