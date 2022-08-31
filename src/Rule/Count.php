<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use Countable;
use Yiisoft\Validator\BeforeValidationInterface;
use Yiisoft\Validator\Rule\Trait\BeforeValidationTrait;
use Yiisoft\Validator\Rule\Trait\LimitTrait;
use Yiisoft\Validator\Rule\Trait\RuleNameTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * Validates that the value contains certain number of items. Can be applied to arrays or classes implementing
 * {@see Countable} interface.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Count implements SerializableRuleInterface, BeforeValidationInterface, SkipOnEmptyInterface
{
    use BeforeValidationTrait;
    use SkipOnEmptyTrait;
    use LimitTrait;
    use RuleNameTrait;

    public function __construct(
        /**
         * @var int|null minimum number of items. null means no minimum number limit. Can't be combined with
         * {@see $exactly}.
         *
         * @see $lessThanMinMessage for the customized message for a value with too few items.
         */
        ?int $min = null,
        /**
         * @var int|null maximum number of items. null means no maximum number limit. Can't be combined with
         * {@see $exactly}.
         *
         * @see $greaterThanMaxMessage for the customized message for a value wuth too many items.
         */
        ?int $max = null,
        /**
         * @var int|null exact number of items. `null` means no strict comparison. Mutually exclusive with {@see $min}
         * and {@see $max}.
         */
        ?int $exactly = null,
        /**
         * @var string user-defined error message used when the value is neither an array nor implementing
         * {@see \Countable} interface.
         *
         * @see Countable
         */
        private string $message = 'This value must be an array or implement \Countable interface.',
        /**
         * @var string user-defined error message used when the number of items is smaller than {@see $min}.
         */
        string $lessThanMinMessage = 'This value must contain at least {min, number} {min, plural, one{item} ' .
        'other{items}}.',
        /**
         * @var string user-defined error message used when the number of items is greater than {@see $max}.
         */
        string $greaterThanMaxMessage = 'This value must contain at most {max, number} {max, plural, one{item} ' .
        'other{items}}.',
        /**
         * @var string user-defined error message used when the number of items does not equal {@see $exactly}.
         */
        string $notExactlyMessage = 'This value must contain exactly {exactly, number} {exactly, plural, one{item} ' .
        'other{items}}.',

        /**
         * @var bool|callable
         */
        private $skipOnEmpty = false,
        private bool $skipOnError = false,
        /**
         * @var Closure(mixed, ValidationContext):bool|null
         */
        private ?Closure $when = null,
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

    public function getOptions(): array
    {
        return array_merge($this->getLimitOptions(), [
            'message' => [
                'message' => $this->getMessage(),
            ],
            'skipOnEmpty' => $this->skipOnEmpty !== false,
            'skipOnError' => $this->skipOnError,
        ]);
    }

    public function getHandlerClassName(): string
    {
        return CountHandler::class;
    }
}
