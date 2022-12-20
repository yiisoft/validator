<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use JetBrains\PhpStorm\Language;
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
 * Validates that the value is a valid email address.
 *
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Email implements RuleWithOptionsInterface, SkipOnErrorInterface, WhenInterface, SkipOnEmptyInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @param string $pattern The regular expression used to validate the value. See {@link https://www.regular-expressions.info/email.html}.
     * @param string $fullPattern The regular expression used to validate email addresses with the name part.
     * This property is used only when {@see $allowName} is `true`.
     * @param string $idnEmailPattern The regular expression used to validate complex emails when {@see $enableIdn} is `true`.
     * @param bool $allowName bool Whether to allow a name in the email address (e.g. "John Smith <john.smith@example.com>").
     * Defaults to `false`. See {@see $fullPattern}.
     * @param bool $checkDns bool Whether to check email's domain exists and has either an A or MX record.
     * Be aware that this check can fail due to temporary DNS problems even if the email address is
     * valid and an email would be deliverable. Defaults to `false`.
     * @param bool $enableIdn Whether validation process should take IDN (internationalized domain names) into account.
     * Defaults to false meaning that validation of emails containing IDN will always fail.
     * Note that in order to use IDN validation you have to install and enable `intl` PHP extension,
     * otherwise an exception would be thrown.
     * @param string $incorrectInputMessage A message used when the input is incorrect.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the label of the attribute being validated.
     * - `{type}`: the type of the attribute being validated.
     * @param string $message A message used when the value is not valid.
     *
     * You may use the following placeholders in the message:
     *
     * - `{attribute}`: the label of the attribute being validated.
     * - `{value}`: the value of the attribute being validated.
     * @param bool|callable|null $skipOnEmpty Whether to skip this rule if the value validated is empty. See {@see SkipOnEmptyInterface}.
     * @param bool $skipOnError Whether to skip this rule if any of the previous rules gave an error. See {@see SkipOnErrorInterface}.
     * @param Closure|null $when A callable to define a condition for applying the rule. See {@see WhenInterface}.
     * @psalm-param WhenType $when
     */
    public function __construct(
        private string $pattern = '/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/',
        private string $fullPattern = '/^[^@]*<[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?>$/',
        private string $idnEmailPattern = '/^([a-zA-Z0-9._%+-]+)@((\[\d{1,3}\.\d{1,3}\.\d{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|\d{1,3})(\]?)$/',
        private bool $allowName = false,
        private bool $checkDns = false,
        private bool $enableIdn = false,
        private string $incorrectInputMessage = 'The value must have a string type.',
        private string $message = 'This value is not a valid email address.',
        private $skipOnEmpty = null,
        private bool $skipOnError = false,
        private Closure|null $when = null,
    ) {
        if ($enableIdn && !function_exists('idn_to_ascii')) {
            // Tested via separate CI configuration (see ".github/workflows/build.yml").
            // @codeCoverageIgnoreStart
            throw new RuntimeException('In order to use IDN validation intl extension must be installed and enabled.');
            // @codeCoverageIgnoreEnd
        }
    }

    public function getName(): string
    {
        return 'email';
    }

    /**
     * Get the regular expression used to validate the value.
     *
     * @return string The regular expression.
     *
     * @see $pattern
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * Get the regular expression used to validate email addresses with the name part.
     *
     * @return string The regular expression.
     *
     * @see $fullPattern
     */
    public function getFullPattern(): string
    {
        return $this->fullPattern;
    }

    /**
     * Get the regular expression used to validate complex emails when {@see $enableIdn} is `true`.
     *
     * @return string The regular expression.
     *
     * @see $idnEmailPattern
     */
    public function getIdnEmailPattern(): string
    {
        return $this->idnEmailPattern;
    }

    /**
     * Whether to allow a name in the email address (e.g. "John Smith <john.smith@example.com>").
     *
     * @return bool Whether to allow a name in the email address.
     *
     * @see $allowName
     */
    public function isNameAllowed(): bool
    {
        return $this->allowName;
    }

    /**
     * Whether to check email's domain exists and has either an A or MX record.
     *
     * @return bool Whether to check email's domain exists and has either an A or MX record.
     *
     * @see $checkDns
     */
    public function shouldCheckDns(): bool
    {
        return $this->checkDns;
    }

    /**
     * Whether validation process should take IDN (internationalized domain names) into account.
     *
     * @return bool Whether validation process should take IDN (internationalized domain names) into account.
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
     * @return string A message used when the input is incorrect.
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
     * @return string A message used when the value is not valid.
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
            'fullPattern' => $this->fullPattern,
            'idnEmailPattern' => $this->idnEmailPattern,
            'allowName' => $this->allowName,
            'checkDns' => $this->checkDns,
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
        return EmailHandler::class;
    }
}
