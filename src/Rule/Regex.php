<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use InvalidArgumentException;
use JetBrains\PhpStorm\Language;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\RuleWithOptionsInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\WhenInterface;

/**
 * Defines validation options to check that the value matches the pattern specified in constructor.
 *
 * If the {@see Regex::$not} is used, the rule will ensure the value do NOT match the pattern.
 *
 * @see RegexHandler
 *
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Regex implements RuleWithOptionsInterface, SkipOnErrorInterface, WhenInterface, SkipOnEmptyInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @var string The regular expression to be matched with.
     * @psalm-var non-empty-string
     */
    private string $pattern;

    /**
     * @param string $pattern The regular expression to be matched with.
     * @param bool $not Whether to invert the validation logic. Defaults to `false`. If set to `true`, the regular
     * expression defined via {@see $pattern} should NOT match the value.
     * @param string $incorrectInputMessage A message used when the input is incorrect.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{type}`: the type of the value being validated.
     * @param string $message A message used when the value does not match regular expression.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{value}`: the value of the attribute being validated.
     * @param bool|callable|null $skipOnEmpty Whether to skip this rule if the value validated is empty.
     * See {@see SkipOnEmptyInterface}.
     * @param bool $skipOnError Whether to skip this rule if any of the previous rules gave an error.
     * See {@see SkipOnErrorInterface}.
     * @param Closure|null $when A callable to define a condition for applying the rule.
     * See {@see WhenInterface}.
     *
     * @psalm-param WhenType $when
     */
    public function __construct(
        #[Language('RegExp')]
        string $pattern,
        private bool $not = false,
        private string $incorrectInputMessage = 'The value must be a string.',
        private string $message = 'Value is invalid.',
        private mixed $skipOnEmpty = null,
        private bool $skipOnError = false,
        private Closure|null $when = null,
    ) {
        if ($pattern === '') {
            throw new InvalidArgumentException('Pattern can\'t be empty.');
        }

        $this->pattern = $pattern;
    }

    public function getName(): string
    {
        return 'regex';
    }

    /**
     * Get the regular expression to be matched with.
     *
     * @return string The regular expression.
     * @psalm-return non-empty-string
     *
     * @see $pattern
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * Get whether to invert the validation logic.
     *
     * @return bool Whether to invert the validation logic.
     *
     * @see $not
     */
    public function isNot(): bool
    {
        return $this->not;
    }

    /**
     * Get a message used when the input is incorrect.
     *
     * @return string Error message.
     *
     * @see $incorrectInputMessage
     */
    public function getIncorrectInputMessage(): string
    {
        return $this->incorrectInputMessage;
    }

    /**
     * Get a message used when the value does not match regular expression.
     *
     * @return string Error message.
     *
     * @see $message
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

    public function getHandler(): string
    {
        return RegexHandler::class;
    }
}
