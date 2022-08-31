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
 * Validates that the value is among a list of values.
 *
 * The range can be specified via constructor.
 * If the {@see InRange::$not} is called, the rule will ensure the value is NOT among the specified range.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class InRange implements SerializableRuleInterface, BeforeValidationInterface, SkipOnEmptyInterface
{
    use BeforeValidationTrait;
    use SkipOnEmptyTrait;
    use RuleNameTrait;

    public function __construct(
        private iterable $range,
        /**
         * @var bool whether the comparison is strict (both type and value must be the same)
         */
        private bool $strict = false,
        /**
         * @var bool whether to invert the validation logic. Defaults to false. If set to `true`, the value should NOT
         * be among the list of values passed via constructor.
         */
        private bool $not = false,
        private string $message = 'This value is invalid.',

        /**
         * @var bool|callable
         */
        private $skipOnEmpty = false,
        private bool $skipOnError = false,
        /**
         * @var Closure(mixed, ValidationContext):bool|null
         */
        private ?Closure $when = null,
    )
    {
    }

    /**
     * @return iterable
     */
    public function getRange(): iterable
    {
        return $this->range;
    }

    /**
     * @return bool
     */
    public function isStrict(): bool
    {
        return $this->strict;
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
    public function getMessage(): string
    {
        return $this->message;
    }

    public function getOptions(): array
    {
        return [
            'range' => $this->range,
            'strict' => $this->strict,
            'not' => $this->not,
            'message' => [
                'message' => $this->message,
            ],
            'skipOnEmpty' => $this->skipOnEmpty !== false,
            'skipOnError' => $this->skipOnError,
        ];
    }

    public function getHandlerClassName(): string
    {
        return InRangeHandler::class;
    }
}
