<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use RuntimeException;
use Yiisoft\Validator\ValidationContext;

/**
 * Validates if the specified value is equal to another value or attribute.
 *
 * The value being validated with {@see Equal::$targetValue} or {@see Equal::$targetAttribute}, which
 * is set in the constructor.
 *
 * The default validation function is based on string values, which means the values
 * are checked byte by byte. When validating numbers, make sure to change {@see Equal::$type} to
 * {@see Equal::TYPE_NUMBER} to enable numeric validation.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Equal extends Compare
{
    public function __construct(
        /**
         * @var mixed The constant value to be equal to. When both this property
         * and {@see $targetAttribute} are set, this property takes precedence.
         */
        private $targetValue = null,
        /**
         * @var mixed The attribute to be equal to. When both this property
         * and {@see $targetValue} are set, the {@see $targetValue} takes precedence.
         */
        private ?string $targetAttribute = null,
        /**
         * @var string|null User-defined error message.
         */
        private ?string $message = null,
        /**
         * @var string The type of the values being compared.
         */
        private string $type = self::TYPE_STRING,
        /**
         * @var bool Whether this validator strictly check.
         */
        private bool $strict = false,

        /**
         * @var bool|callable
         */
        $skipOnEmpty = false,
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
            operator: $this->strict ? '===' : '==',
            skipOnEmpty: $skipOnEmpty,
            skipOnError: $this->skipOnError,
            when: $this->when
        );
    }

    public function getName(): string
    {
        return 'equal';
    }
}
