<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use RuntimeException;
use Yiisoft\Validator\WhenInterface;

/**
 * Validates if the specified value is not equal to another value or attribute.
 *
 * The value being validated with {@see NotEqual::$targetValue} or {@see NotEqual::$targetAttribute}, which
 * is set in the constructor.
 *
 * The default validation function is based on string values, which means the values
 * are checked byte by byte. When validating numbers, make sure to change {@see NotEqual::$type} to
 * {@see NotEqual::TYPE_NUMBER} to enable numeric validation.
 *
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class NotEqual extends Compare
{
    public function __construct(
        /**
         * @var scalar|null The constant value to not be equal to. When both this property and
         * {@see $targetAttribute} are set, this property takes precedence.
         */
        private int|float|string|bool|null $targetValue = null,
        /**
         * @var string|null The constant value to not be equal to. When both this property and
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
         * @var string The type of the values being validated.
         */
        private string $type = self::TYPE_STRING,
        /**
         * @var bool Whether this validator strictly check.
         */
        private bool $strict = false,
        bool|callable|null $skipOnEmpty = false,
        private bool $skipOnError = false,
        /**
         * @var WhenType
         */
        private Closure|null $when = null,
    ) {
        if ($this->targetValue === null && $this->targetAttribute === null) {
            throw new RuntimeException('Either "targetValue" or "targetAttribute" must be specified.');
        }

        parent::__construct(
            targetValue: $this->targetValue,
            targetAttribute: $this->targetAttribute,
            incorrectInputMessage: $this->incorrectInputMessage,
            incorrectDataSetTypeMessage: $this->incorrectDataSetTypeMessage,
            message: $this->message,
            type: $this->type,
            operator: $this->strict ? '!==' : '!=',
            skipOnEmpty: $skipOnEmpty,
            skipOnError: $this->skipOnError,
            when: $this->when,
        );
    }

    public function getName(): string
    {
        return 'notEqual';
    }
}
