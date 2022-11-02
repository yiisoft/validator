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

    /**
     * @param string[] $attributes The list of required attributes that will be checked.
     * @param int $min The minimum required quantity of filled attributes to pass the validation.
     * Defaults to 1.
     * @param string $incorrectInputMessage = 'Value must be array or iterable.',
     * @param string $message Message to display in case of error.
     * @param bool|callable|null $skipOnEmpty
     * @param bool $skipOnError
     * @param Closure(mixed, ValidationContext):bool|null $when
     */
    public function __construct(
        /**
         * @var string[]
         */
        private array $attributes,
        private int $min = 1,
        private string $incorrectInputMessage = 'Value must be an array or an object.',
        private string $message = 'The model is not valid. Must have at least "{min}" filled attributes.',
        private mixed $skipOnEmpty = null,
        private bool $skipOnError = false,
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
