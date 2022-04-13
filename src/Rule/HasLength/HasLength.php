<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\HasLength;

use Attribute;
use Closure;

/**
 * Validates that the value is of certain length.
 *
 * Note, this rule should only be used with strings.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class HasLength
{
    public function __construct(
        /**
         * @var int|null minimum length. null means no minimum length limit.
         *
         * @see $tooShortMessage for the customized message for a too short string.
         */
        public ?int $min = null,
        /**
         * @var int|null maximum length. null means no maximum length limit.
         *
         * @see $tooLongMessage for the customized message for a too long string.
         */
        public ?int $max = null,
        /**
         * @var string user-defined error message used when the value is not a string.
         */
        public string $message = 'This value must be a string.',
        /**
         * @var string user-defined error message used when the length of the value is smaller than {@see $min}.
         */
        public string $tooShortMessage = 'This value should contain at least {min, number} {min, plural, one{character} other{characters}}.',
        /**
         * @var string user-defined error message used when the length of the value is greater than {@see $max}.
         */
        public string $tooLongMessage = 'This value should contain at most {max, number} {max, plural, one{character} other{characters}}.',
        /**
         * @var string the encoding of the string value to be validated (e.g. 'UTF-8').
         * If this property is not set, application wide encoding will be used.
         */
        public string $encoding = 'UTF-8',
        public bool $skipOnEmpty = false,
        public bool $skipOnError = false,
        public ?Closure $when = null
    ) {
    }

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
                'message' => $this->tooLongMessage, ['max' => $this->max],
                'parameters' => ['max' => $this->max],
            ],
            'encoding' => $this->encoding,
        ];
    }
}
