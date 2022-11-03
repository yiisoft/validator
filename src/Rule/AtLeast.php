<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\WhenInterface;

/**
 * Checks if at least {@see AtLeast::$min} of many attributes are filled.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class AtLeast implements SerializableRuleInterface, SkipOnErrorInterface, WhenInterface, SkipOnEmptyInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    public function __construct(
        /**
         * @var string[] The list of required attributes that will be checked.
         */
        private array $attributes,
        /**
         * @var int The minimum required quantity of filled attributes to pass the validation. Defaults to 1.
         */
        private int $min = 1,
        private string $incorrectInputMessage = 'Value must be an array or an object.',
        /**
         * @var string Message to display in case of error.
         */
        private string $message = 'The model is not valid. Must have at least "{min}" filled attributes.',
        /**
         * @var bool|callable|null
         */
        private mixed $skipOnEmpty = null,
        /**
         * @var bool
         */
        private bool $skipOnError = false,
        /**
         * @var Closure(mixed, ValidationContext):bool|null
         */
        private ?Closure $when = null
    ) {
    }

    public function getName(): string
    {
        return 'atLeast';
    }

    /**
     * @return string[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getMin(): int
    {
        return $this->min;
    }

    public function getIncorrectInputMessage(): string
    {
        return $this->incorrectInputMessage;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getOptions(): array
    {
        return [
            'attributes' => $this->attributes,
            'min' => $this->min,
            'incorrectInputMessage' => $this->incorrectInputMessage,
            'message' => [
                'message' => $this->message,
                'parameters' => ['min' => $this->min],
            ],
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ];
    }

    public function getHandlerClassName(): string
    {
        return AtLeastHandler::class;
    }
}
