<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use Yiisoft\Validator\BeforeValidationInterface;
use Yiisoft\Validator\Rule\Trait\BeforeValidationTrait;
use Yiisoft\Validator\Rule\Trait\RuleNameTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * Validates that the value matches the pattern specified in constructor.
 *
 * If the {@see Regex::$not} is used, the rule will ensure the value do NOT match the pattern.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Regex implements SerializableRuleInterface, BeforeValidationInterface, SkipOnEmptyInterface
{
    use BeforeValidationTrait;
    use SkipOnEmptyTrait;
    use RuleNameTrait;

    public function __construct(
        /**
         * @var string the regular expression to be matched with
         */
        private string $pattern,
        /**
         * @var bool whether to invert the validation logic. Defaults to `false`. If set to `true`, the regular
         * expression defined via {@see $pattern} should NOT match the value.
         */
        private bool $not = false,
        private string $incorrectInputMessage = 'Value should be string.',
        private string $message = 'Value is invalid.',

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
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * @return bool
     */
    public function isNot(): bool
    {
        return $this->not;
    }

    /**
     * @return string
     */
    public function getIncorrectInputMessage(): string
    {
        return $this->incorrectInputMessage;
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
        return [
            'pattern' => $this->pattern,
            'not' => $this->not,
            'incorrectInputMessage' => [
                'message' => $this->incorrectInputMessage,
            ],
            'message' => [
                'message' => $this->message,
            ],
            'skipOnEmpty' => $this->skipOnEmpty !== false,
            'skipOnError' => $this->skipOnError,
        ];
    }

    public function getHandlerClassName(): string
    {
        return RegexHandler::class;
    }
}
