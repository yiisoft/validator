<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
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
 * Validates that the value is a number.
 *
 * The format of the number must match the regular expression specified in {@see Number::$integerPattern}
 * or {@see Number::$numberPattern}. Optionally, you may configure the {@see Number::min()} and {@see Number::max()}
 * to ensure the number is within certain range.
 *
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Number implements
    RuleWithOptionsInterface,
    SkipOnErrorInterface,
    WhenInterface,
    SkipOnEmptyInterface,
    LimitInterface
{
    use LimitTrait;
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    public function __construct(
        /**
         * @var bool whether the value can only be an integer. Defaults to `false`.
         */
        private bool $asInteger = false,
        /**
         * @var float|int|null lower limit of the number. Defaults to `null`, meaning no lower limit. Can't be combined
         * with {@see $exactly}.
         *
         * @see $lessThanMin for the message used when the number is too small.
         */
        int|float|null $min = null,
        /**
         * @var float|int|null upper limit of the number. Defaults to `null`, meaning no upper limit. Can't be combined
         * with {@see $exactly}.
         *
         * @see $greaterThanMax for the message used when the number is too big.
         */
        int|float|null $max = null,
        /**
         * @var float|int|null exact number. `null` means no strict comparison. Mutually exclusive with {@see $min}
         * and {@see $max}.
         *
         * @see $notExactlyMessage for the message used when the number does not equal to the set one.
         */
        int|float|null $exactly = null,
        private string $incorrectInputMessage = 'The allowed types are integer, float and string.',
        /**
         * @var string error message used when the value is smaller than {@link $min}.
         */
        private string $lessThanMinMessage = 'Value must be no less than {min}.',
        /**
         * @var string error message used when the value is bigger than {@link $max}.
         */
        private string $greaterThanMaxMessage = 'Value must be no greater than {max}.',
        /**
         * @var string error message used when the value does not equal {@see $exactly}.
         */
        private string $notExactlyMessage = 'Value must be equal to {exactly}.',
        /**
         * @var string the regular expression for matching integers.
         */
        private string $integerPattern = '/^\s*[+-]?\d+\s*$/',
        /**
         * @var string the regular expression for matching numbers. It defaults to a pattern
         * that matches floating numbers with optional exponential part (e.g. -1.23e-10).
         */
        private string $numberPattern = '/^\s*[-+]?\d*\.?\d+([eE][-+]?\d+)?\s*$/',

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
            $notExactlyMessage,
            requireLimits: false,
            allowNegativeLimits: true,
        );
    }

    public function getName(): string
    {
        return 'number';
    }

    public function isAsInteger(): bool
    {
        return $this->asInteger;
    }

    public function getIncorrectInputMessage(): string
    {
        return $this->incorrectInputMessage;
    }

    public function getIntegerPattern(): string
    {
        return $this->integerPattern;
    }

    public function getNumberPattern(): string
    {
        return $this->numberPattern;
    }

    public function getNotNumberMessage(): string
    {
        return $this->asInteger ? 'Value must be an integer.' : 'Value must be a number.';
    }

    public function getOptions(): array
    {
        return array_merge($this->getLimitOptions(), [
            'asInteger' => $this->asInteger,
            'incorrectInputMessage' => [
                'template' => $this->incorrectInputMessage,
                'parameters' => [],
            ],
            'notNumberMessage' => [
                'template' => $this->getNotNumberMessage(),
                'parameters' => [],
            ],
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
            'integerPattern' => $this->integerPattern,
            'numberPattern' => $this->numberPattern,
        ]);
    }

    public function getHandlerClassName(): string
    {
        return NumberHandler::class;
    }
}
