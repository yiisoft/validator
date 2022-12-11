<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use Countable;
use Yiisoft\Validator\AfterInitAttributeEventInterface;
use Yiisoft\Validator\LimitInterface;
use Yiisoft\Validator\Rule\Trait\LimitTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\RuleWithOptionsInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\WhenInterface;

/**
 * Validates that the value contains certain number of items. Can be applied to arrays or classes implementing
 * {@see Countable} interface.
 *
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Count implements
    RuleWithOptionsInterface,
    SkipOnErrorInterface,
    WhenInterface,
    SkipOnEmptyInterface,
    LimitInterface,
    AfterInitAttributeEventInterface
{
    use LimitTrait;
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    private ?object $objectValidated = null;

    public function __construct(
        /**
         * @var int|null minimum number of items. null means no minimum number limit. Can't be combined with
         * {@see $exactly}.
         *
         * @see $lessThanMinMessage for the customized message for a value with too few items.
         */
        int|null $min = null,
        /**
         * @var int|null maximum number of items. null means no maximum number limit. Can't be combined with
         * {@see $exactly}.
         *
         * @see $greaterThanMaxMessage for the customized message for a value wuth too many items.
         */
        int|null $max = null,
        /**
         * @var int|null exact number of items. `null` means no strict comparison. Mutually exclusive with {@see $min}
         * and {@see $max}.
         */
        int|null $exactly = null,
        /**
         * @var string user-defined error message used when the value is neither an array nor implementing
         * {@see \Countable} interface.
         *
         * @see Countable
         */
        private string $incorrectInputMessage = 'This value must be an array or implement \Countable interface.',
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
         * @var bool|callable|null
         */
        private $skipOnEmpty = null,
        private bool $skipOnError = false,
        /**
         * @var WhenType
         */
        private Closure|null $when = null,
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

    public function getName(): string
    {
        return 'count';
    }

    public function getIncorrectInputMessage(): string
    {
        return $this->incorrectInputMessage;
    }

    public function getObjectValidated(): ?object
    {
        return $this->objectValidated;
    }

    public function getOptions(): array
    {
        return array_merge($this->getLimitOptions(), [
            'incorrectInputMessage' => [
                'template' => $this->getIncorrectInputMessage(),
                'parameters' => [],
            ],
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ]);
    }

    public function getHandlerClassName(): string
    {
        return CountHandler::class;
    }

    public function afterInitAttribute(object $object, int $target): void
    {
        if ($target === Attribute::TARGET_CLASS) {
            $this->objectValidated = $object;
        }
    }
}
