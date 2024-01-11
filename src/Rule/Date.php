<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Closure;
use Attribute;
use Yiisoft\Validator\WhenInterface;
use Yiisoft\Validator\DumpedRuleInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;

/**
 * @see DateHandler
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Date implements DumpedRuleInterface, SkipOnErrorInterface, WhenInterface, SkipOnEmptyInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @param string $format The format of the date.
     * @param string $message A message used when the value is not valid.
     *You may use the following placeholders in the message:
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{value}`: the value of the attribute being validated.
     * @param string $incorrectInputMessage A message used when the input is incorrect.
     * You may use the following placeholders in the message:
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{type}`: the type of the value being validated.
     * @param bool|callable|null $skipOnEmpty Whether to skip this rule if the value validated is empty. See {@see SkipOnEmptyInterface}.
     * @param bool $skipOnError Whether to skip this rule if any of the previous rules gave an error. See {@see SkipOnErrorInterface}.
     * @param Closure|null $when A callable to define a condition for applying the rule. See {@see WhenInterface}.
     * @psalm-param WhenType $when
     */
    public function __construct(
        private string $format = 'Y-m-d',
        private string $pattern = '/^(?=.*Y)(?=.*[mM])(?=.*d).*[Ymd](-|\/|.)[Ymd]\1[Ymd]$/',
        private string $incorrectInputMessage = 'The {attribute} must be a date.',
        private string $message = 'The {attribute} is not a valid date.',
        private mixed $skipOnEmpty = null,
        private bool $skipOnError = false,
        private ?Closure $when = null,
    ) {
    }


    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * Get a message used when the input is incorrect.
     *
     * @return string A message used when the input is incorrect.
     * @see $incorrectInputMessage
     */
    public function getIncorrectInputMessage(): string
    {
        return $this->incorrectInputMessage;
    }

    public function getOptions(): array
    {
        return [
            'format' => $this->format,
            'pattern' => $this->pattern,
            'incorrectInputMessage' => [
                'template' => $this->incorrectInputMessage,
                'parameters' => [],
            ],
            'message' => [
                'template' => $this->message,
                'parameters' => [],
            ],
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ];
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getName(): string
    {
        return self::class;
    }

    public function getHandler(): string
    {
        return DateHandler::class;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }
}
