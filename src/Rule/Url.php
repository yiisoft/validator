<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use InvalidArgumentException;
use RuntimeException;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\RuleWithOptionsInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\WhenInterface;

use function function_exists;

/**
 * Defines validation options for a value that is a valid HTTP or HTTPS URL.
 *
 * Note that the handler only checks if the URL scheme and host parts are correct.
 * It does not check the remaining parts of a URL.
 *
 * @psalm-import-type WhenType from WhenInterface
 *
 * @see UrlHandler
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Url implements RuleWithOptionsInterface, SkipOnErrorInterface, WhenInterface, SkipOnEmptyInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @var string The regular expression used to validate the value.
     * @psalm-var non-empty-string
     */
    private string $pattern;

    /**
     * @param string $pattern The regular expression used to validate the value.
     * The pattern may contain a `{schemes}` token that will be replaced
     * by a regular expression which represents the {@see $schemes}.
     *
     * Note that if you want to reuse the pattern in HTML5 input, it should have `^` and `$`, should not have any
     * modifiers and should not be case-insensitive.
     * @param string[] $validSchemes List of URI schemes which should be considered valid. By default, http and https
     * are considered to be valid schemes.
     * @param bool $enableIdn Whether the validation process must take
     * {@link https://en.wikipedia.org/wiki/Internationalized_domain_name IDN (internationalized domain names)}
     * into account . Defaults to `false` meaning that validation of URLs containing IDN will always
     * fail. Note that in order to use IDN validation you have to install and enable `intl` PHP
     * extension, otherwise an exception will be thrown.
     * @param string $incorrectInputMessage A message used when the input is incorrect.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{type}`: the value's type.
     * @param string $message @var string A message used when the value is not valid.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{value}`: the value of the attribute being validated.
     * @param bool|callable|null $skipOnEmpty Whether to skip this rule if the validated value is empty. See {@see SkipOnEmptyInterface}.
     * @param bool $skipOnError Whether to skip this rule if any of the previous rules gave an error. See {@see SkipOnErrorInterface}.
     * @param Closure|null $when A callable to define a condition for applying the rule. See {@see WhenInterface}.
     *
     * @psalm-param WhenType $when
     *
     * @throws RuntimeException If intl extension is not enabled and {@see $enableIdn} is true.
     */
    public function __construct(
        string $pattern = '/^{schemes}:\/\/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+)(?::\d{1,5})?([?\/#].*$|$)/',
        private array $validSchemes = ['http', 'https'],
        private bool $enableIdn = false,
        private string $incorrectInputMessage = 'The value must be a string.',
        private string $message = 'This value is not a valid URL.',
        private mixed $skipOnEmpty = null,
        private bool $skipOnError = false,
        private Closure|null $when = null,
    ) {
        $pattern = $this->preparePattern($pattern);
        if ($pattern === '') {
            throw new InvalidArgumentException('Pattern can\'t be empty.');
        }

        /**
         * @psalm-var non-empty-string $pattern
         */
        $this->pattern = $pattern;

        if ($enableIdn && !function_exists('idn_to_ascii')) {
            // Tested via separate CI configuration (see ".github/workflows/build.yml").
            // @codeCoverageIgnoreStart
            throw new RuntimeException('In order to use IDN validation intl extension must be installed and enabled.');
            // @codeCoverageIgnoreEnd
        }
    }

    public function getName(): string
    {
        return 'url';
    }

    /**
     * Get ready to use regular expression pattern applied for URL validation.
     *
     * @return string Regular expression pattern applied for URL validation.
     * @psalm-return non-empty-string
     *
     * @see $pattern
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * Get valid URI schemas.
     *
     * @return string[] List of URI schemes which should be considered valid. By default, http and https
     * are considered to be valid schemes.
     *
     * @see $validSchemes
     */
    public function getValidSchemes(): array
    {
        return $this->validSchemes;
    }

    /**
     * Whether the validation process must take
     * {@link https://en.wikipedia.org/wiki/Internationalized_domain_name IDN (internationalized domain names)}
     * into account. `false` means that validation of URLs containing IDN will always
     * fail. Note that in order to use IDN validation you have to install and enable `intl` PHP
     * extension, otherwise an exception will be thrown.
     *
     * @return bool Whether to enable IDN validation.
     *
     * @see $enableIdn
     */
    public function isIdnEnabled(): bool
    {
        return $this->enableIdn;
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
     * Get a message used when the value is not valid.
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
            'pattern' => $this->getPattern(),
            'validSchemes' => $this->validSchemes,
            'enableIdn' => $this->enableIdn,
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
        return UrlHandler::class;
    }

    private function preparePattern(string $pattern): string
    {
        return str_replace('{schemes}', '((?i)' . implode('|', $this->validSchemes) . ')', $pattern);
    }
}
