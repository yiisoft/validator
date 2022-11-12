<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\SkipOnEmptyCallback\SkipOnEmpty;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\WhenInterface;

/**
 * Validates that the specified value is neither null nor empty.
 *
 * @psalm-type EmptyCallbackType = callable(mixed,bool):bool
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Required implements SerializableRuleInterface, SkipOnErrorInterface, WhenInterface
{
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @var callable
     * @psalm-var EmptyCallbackType
     */
    private $emptyCallback;

    /**
     * @psalm-param EmptyCallbackType|null $emptyCallback
     */
    public function __construct(
        private string $message = 'Value cannot be blank.',
        private string $notPassedMessage = 'Value not passed.',
        callable|null $emptyCallback = null,
        private bool $skipOnError = false,
        /**
         * @var Closure(mixed, ValidationContext):bool|null
         */
        private ?Closure $when = null,
    ) {
        $this->emptyCallback = $emptyCallback ?? new SkipOnEmpty(trimString: true);
    }

    public function getName(): string
    {
        return 'required';
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getNotPassedMessage(): string
    {
        return $this->notPassedMessage;
    }

    /**
     * @psalm-return EmptyCallbackType
     */
    public function getEmptyCallback(): callable
    {
        return $this->emptyCallback;
    }

    public function getOptions(): array
    {
        return [
            'message' => $this->message,
            'notPassedMessage' => $this->notPassedMessage,
            'skipOnError' => $this->skipOnError,
        ];
    }

    public function getHandlerClassName(): string
    {
        return RequiredHandler::class;
    }
}
