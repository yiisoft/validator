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

    public function __construct(
        #[Language('RegExp')]
        /**
         * @var string The regular expression used to validate the value.
         *
         * @link https://www.regular-expressions.info/email.html
         */
        private string $pattern = '/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/',
        #[Language('RegExp')]
        /**
         * @var string The regular expression used to validate email addresses with the name part.
         * This property is used only when {@see $allowName} is `true`.
         *
         * @see $allowName
         */
        private string $fullPattern = '/^[^@]*<[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?>$/',
        #[Language('RegExp')]
        /**
         * @var string The regular expression used to validate complex emails when {@see $enableIDN} is `true`.
         */
        private string $idnEmailPattern = '/^([a-zA-Z0-9._%+-]+)@((\[\d{1,3}\.\d{1,3}\.\d{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|\d{1,3})(\]?)$/',
        /**
         * @var bool Whether to allow a name in the email address (e.g. "John Smith <john.smith@example.com>").
         * Defaults to `false`.
         *
         * @see $fullPattern
         */
        private bool $allowName = false,
        /**
         * @var bool Whether to check email's domain exists and has either an A or MX record.
         * Be aware that this check can fail due to temporary DNS problems even if the email address is
         * valid and an email would be deliverable. Defaults to `false`.
         */
        private bool $checkDNS = false,
        /**
         * @var bool Whether validation process should take IDN (internationalized domain names) into account.
         * Defaults to false meaning that validation of emails containing IDN will always fail.
         * Note that in order to use IDN validation you have to install and enable `intl` PHP extension,
         * otherwise an exception would be thrown.
         */
        private bool $enableIDN = false,
        /**
         * @var string A message used when the input is incorrect.
         *
         * You may use the following placeholders in the message:
         *
         * - `{attribute}`: the label of the attribute being validated.
         * - `{value}`: the value of the attribute being validated.
         */
        private string $incorrectInputMessage = 'The value must have a string type.',
        /**
         * @var string A message used when the value is not valid.
         *
         * You may use the following placeholders in the message:
         *
         * - `{attribute}`: the label of the attribute being validated.
         * - `{value}`: the value of the attribute being validated.
         */
        private string $message = 'This value is not a valid email address.',
        /**
         * @var bool|callable|null Whether to skip this rule if the value validated is empty.
         *
         * @see SkipOnEmptyInterface
         */
        private $skipOnEmpty = null,
        /**
         * @var bool Whether to skip this rule if any of the previous rules gave an error.
         */
        private bool $skipOnError = false,
        /**
         * @var Closure|null A callable to define a condition for applying the rule.
         * @psalm-var WhenType
         *
         * @see WhenInterface
         */
        private Closure|null $when = null,
    ) {
        if ($enableIDN && !function_exists('idn_to_ascii')) {
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
     * @return string The regular expression used to validate the value.
     *
     * @see $pattern
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * @return string The regular expression used to validate email addresses with the name part.
     *
     * @see $fullPattern
     */
    public function getFullPattern(): string
    {
        return $this->fullPattern;
    }

    /**
     * @return string The regular expression used to validate complex emails when {@see $enableIDN} is `true`.
     *
     * @see $idnEmailPattern
     */
    public function getIdnEmailPattern(): string
    {
        return $this->idnEmailPattern;
    }

    /**
     * @return bool Whether to allow a name in the email address (e.g. "John Smith <john.smith@example.com>").
     *
     * @see $allowName
     */
    public function isNameAllowed(): bool
    {
        return $this->allowName;
    }

    /**
     * @return bool Whether to check email's domain exists and has either an A or MX record.
     *
     * @see $checkDNS
     */
    public function shouldCheckDNS(): bool
    {
        return $this->checkDNS;
    }

    /**
     * @return bool Whether validation process should take IDN (internationalized domain names) into account.
     *
     * @see $enableIDN
     */
    public function isIDNEnabled(): bool
    {
        return $this->enableIDN;
    }

    /**
     * @return string A message used when the input is incorrect.
     *
     * @see $incorrectInputMessage
     */
    public function getIncorrectInputMessage(): string
    {
        return $this->incorrectInputMessage;
    }

    /**
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
            'checkDNS' => $this->checkDNS,
            'enableIDN' => $this->enableIDN,
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
