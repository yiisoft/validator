<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use Yiisoft\Validator\Rule\Trait\LimitTrait;
use Yiisoft\Validator\Rule\Trait\RuleNameTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\WhenInterface;

/**
 * Validates that the value is of certain length.
 *
 * Note, this rule should only be used with strings.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class HasLength implements SerializableRuleInterface, SkipOnErrorInterface, WhenInterface, SkipOnEmptyInterface
{
    use LimitTrait;
    use RuleNameTrait;
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    public function __construct(
        /**
         * @var int|null minimum length. null means no minimum length limit. Can't be combined with
         * {@see $exactly}.
         *
         * @see $lessThanMinMessage for the customized message for a too short string.
         */
        ?int $min = null,
        /**
         * @var int|null maximum length. null means no maximum length limit. Can't be combined with
         * {@see $exactly}.
         *
         * @see $greaterThanMaxMessage for the customized message for a too long string.
         */
        ?int $max = null,
        /**
         * @var int|null exact length. `null` means no strict comparison. Mutually exclusive with {@see $min} and
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

        /**
         * @var bool|callable|null
         */
        private $skipOnEmpty = null,
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

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getEncoding(): string
    {
        return $this->encoding;
    }

    public function getOptions(): array
    {
        return array_merge($this->getLimitOptions(), [
            'message' => [
                'message' => $this->message,
            ],
            'encoding' => $this->encoding,
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ]);
    }

    public function getHandlerClassName(): string
    {
        return HasLengthHandler::class;
    }
}
