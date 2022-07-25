<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\Validator\Rule\Trait\LimitTrait;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\BeforeValidationInterface;
use Yiisoft\Validator\Rule\Trait\BeforeValidationTrait;
use Yiisoft\Validator\Rule\Trait\RuleNameTrait;
use Yiisoft\Validator\ValidationContext;

/**
 * Validates that the value is of certain length.
 *
 * Note, this rule should only be used with strings.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class HasLength implements SerializableRuleInterface, BeforeValidationInterface
{
    use BeforeValidationTrait;
    use LimitTrait;
    use RuleNameTrait;

    public function __construct(
        /**
         * @var int|null minimum length. null means no minimum length limit.
         *
         * @see $lessThanMinMessage for the customized message for a too short string.
         */
        ?int $min = null,
        /**
         * @var int|null maximum length. null means no maximum length limit.
         *
         * @see $greaterThanMaxMessage for the customized message for a too long string.
         */
        ?int $max = null,
        /**
         * @var int|null exact length. null means no strict comparison. Mutually exclusive with {@see $min} and
         * {@see $max}.
         */
        ?int $exactly = null,
        /**
         * @var string user-defined error message used when the value is not a string.
         */
        private string $message = 'This value must be a string.',
        /**
         * @var string user-defined error message used when the length of the value is smaller than {@see $min}.
         */
        string $lessThanMinMessage = 'This value must contain at least {min, number} {min, plural, one{character} ' .
        'other{characters}}.',
        /**
         * @var string user-defined error message used when the length of the value is greater than {@see $max}.
         */
        string $greaterThanMaxMessage = 'This value must contain at most {max, number} {max, plural, one{character} ' .
        'other{characters}}.',
        string $notExactlyMessage = 'This value must contain exactly {exactly, number} {exactly, plural, ' .
        'one{character} other{characters}}.',
        /**
         * @var string the encoding of the string value to be validated (e.g. 'UTF-8').
         * If this property is not set, application wide encoding will be used.
         */
        private string $encoding = 'UTF-8',
        private bool $skipOnEmpty = false,
        private bool $skipOnError = false,
        /**
         * @var Closure(mixed, ValidationContext):bool|null
         */
        private ?Closure $when = null
    ) {
        $this->initLimitProperties(
            $min,
            $max,
            $exactly,
            $lessThanMinMessage,
            $greaterThanMaxMessage,
            $notExactlyMessage
        );
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    #[ArrayShape([
        'min' => 'int|null',
        'max' => 'int|null',
        'message' => 'string[]',
        'lessThanMinMessage' => 'array',
        'greaterThanMaxMessage' => 'array',
        'notExactlyMessage' => 'array',
        'encoding' => 'string',
        'skipOnEmpty' => 'bool',
        'skipOnError' => 'bool',
    ])]
    public function getOptions(): array
    {
        return array_merge($this->getLimitOptions(), [
            'message' => [
                'message' => $this->message,
            ],
            'encoding' => $this->encoding,
            'skipOnEmpty' => $this->skipOnEmpty,
            'skipOnError' => $this->skipOnError,
        ]);
    }

    public function getHandlerClassName(): string
    {
        return HasLengthHandler::class;
    }
}
