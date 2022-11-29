<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\RuleWithOptionsInterface;
use Yiisoft\Validator\EmptyCriteria\WhenEmpty;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\WhenInterface;

/**
 * Validates that the specified value is neither null nor empty.
 *
 * @psalm-type EmptyCriteriaType = callable(mixed,bool):bool
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Required implements RuleWithOptionsInterface, SkipOnErrorInterface, WhenInterface
{
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @var callable
     * @psalm-var EmptyCriteriaType
     */
    private $emptyCriteria;

    /**
     * @psalm-param EmptyCriteriaType|null $emptyCriteria
     */
    public function __construct(
        private string $message = 'Value cannot be blank.',
        private string $notPassedMessage = 'Value not passed.',
        callable|null $emptyCriteria = null,
        private bool $skipOnError = false,
        /**
         * @var Closure(mixed, ValidationContext):bool|null
         */
        private ?Closure $when = null,
    ) {
        $this->emptyCriteria = $emptyCriteria ?? new WhenEmpty(trimString: true);
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
     * @psalm-return EmptyCriteriaType
     */
    public function getEmptyCriteria(): callable
    {
        return $this->emptyCriteria;
    }

    public function getOptions(): array
    {
        return [
            'message' => [
                'template' => $this->message,
                'parameters' => [],
            ],
            'notPassedMessage' => [
                'template' => $this->notPassedMessage,
                'parameters' => [],
            ],
            'skipOnError' => $this->skipOnError,
        ];
    }

    public function getHandlerClassName(): string
    {
        return RequiredHandler::class;
    }
}
