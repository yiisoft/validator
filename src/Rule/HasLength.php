<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\Validator\PreValidatableRuleInterface;
use Yiisoft\Validator\Rule\Trait\PreValidatableTrait;
use Yiisoft\Validator\Rule\Trait\RuleNameTrait;
use Yiisoft\Validator\Rule\Trait\HandlerClassNameTrait;
use Yiisoft\Validator\ParametrizedRuleInterface;

/**
 * Validates that the value is of certain length.
 *
 * Note, this rule should only be used with strings.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class HasLength implements ParametrizedRuleInterface, PreValidatableRuleInterface
{
    use HandlerClassNameTrait;
    use PreValidatableTrait;
    use RuleNameTrait;

    public function __construct(
        /**
         * @var int|null minimum length. null means no minimum length limit.
         *
         * @see $tooShortMessage for the customized message for a too short string.
         */
        private ?int $min = null,
        /**
         * @var int|null maximum length. null means no maximum length limit.
         *
         * @see $tooLongMessage for the customized message for a too long string.
         */
        private ?int $max = null,
        /**
         * @var string user-defined error message used when the value is not a string.
         */
        private string $message = 'This value must be a string.',
        /**
         * @var string user-defined error message used when the length of the value is smaller than {@see $min}.
         */
        private string $tooShortMessage = 'This value should contain at least {min, number} {min, plural, one{character} other{characters}}.',
        /**
         * @var string user-defined error message used when the length of the value is greater than {@see $max}.
         */
        private string $tooLongMessage = 'This value should contain at most {max, number} {max, plural, one{character} other{characters}}.',
        /**
         * @var string the encoding of the string value to be validated (e.g. 'UTF-8').
         * If this property is not set, application wide encoding will be used.
         */
        private string $encoding = 'UTF-8',
        private bool $skipOnEmpty = false,
        private bool $skipOnError = false,
        private ?Closure $when = null
    ) {
    }

    /**
     * @return int|null
     */
    public function getMin(): ?int
    {
        return $this->min;
    }

    /**
     * @return int|null
     */
    public function getMax(): ?int
    {
        return $this->max;
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
    public function getTooShortMessage(): string
    {
        return $this->tooShortMessage;
    }

    /**
     * @return string
     */
    public function getTooLongMessage(): string
    {
        return $this->tooLongMessage;
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
        'tooShortMessage' => 'array',
        'tooLongMessage' => 'array',
        'encoding' => 'string',
        'skipOnEmpty' => 'bool',
        'skipOnError' => 'bool',
    ])]
    public function getOptions(): array
    {
        return [
            'min' => $this->min,
            'max' => $this->max,
            'message' => [
                'message' => $this->message,
            ],
            'tooShortMessage' => [
                'message' => $this->tooShortMessage,
                'parameters' => ['min' => $this->min],
            ],
            'tooLongMessage' => [
                'message' => $this->tooLongMessage,
                'parameters' => ['max' => $this->max],
            ],
            'encoding' => $this->encoding,
            'skipOnEmpty' => $this->skipOnEmpty,
            'skipOnError' => $this->skipOnError,
        ];
    }
}
