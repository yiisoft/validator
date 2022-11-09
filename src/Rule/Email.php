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
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\WhenInterface;

use function function_exists;

/**
 * Validates that the value is a valid email address.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Email implements SerializableRuleInterface, SkipOnErrorInterface, WhenInterface, SkipOnEmptyInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    public function __construct(
        #[Language('RegExp')]
        /**
         * @var string the regular expression used to validate value.
         *
         * @link http://www.regular-expressions.info/email.html
         */
        private string $pattern = '/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/',
        #[Language('RegExp')]
        /**
         * @var string the regular expression used to validate email addresses with the name part. This property is used
         * only when {@see $allowName} is `true`.
         *
         * @see $allowName
         */
        private string $fullPattern = '/^[^@]*<[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?>$/',
        #[Language('RegExp')]
        /**
         * @var string the regular expression used to validate complex emails when {@see $enableIDN} is `true`.
         */
        private string $idnEmailPattern = '/^([a-zA-Z0-9._%+-]+)@((\[\d{1,3}\.\d{1,3}\.\d{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|\d{1,3})(\]?)$/',
        /**
         * @var bool whether to allow name in the email address (e.g. "John Smith <john.smith@example.com>"). Defaults
         * to `false`.
         *
         * @see $fullPattern
         */
        private bool $allowName = false,
        /**
         * @var bool whether to check whether the email's domain exists and has either an A or MX record.
         * Be aware that this check can fail due to temporary DNS problems even if the email address is
         * valid and an email would be deliverable. Defaults to `false`.
         */
        private bool $checkDNS = false,
        /**
         * @var bool whether validation process should take into account IDN (internationalized domain
         * names). Defaults to false meaning that validation of emails containing IDN will always fail.
         * Note that in order to use IDN validation you have to install and enable `intl` PHP extension,
         * otherwise an exception would be thrown.
         */
        private bool $enableIDN = false,
        private string $incorrectInputMessage = 'The value must have a string type.',
        private string $message = 'This value is not a valid email address.',

        /**
         * @var bool|callable|null
         */
        private $skipOnEmpty = null,
        private bool $skipOnError = false,
        /**
         * @var Closure(mixed, ValidationContext):bool|null
         */
        private ?Closure $when = null,
    ) {
        if ($enableIDN && !function_exists('idn_to_ascii')) {
            throw new RuntimeException('In order to use IDN validation intl extension must be installed and enabled.');
        }
    }

    public function getName(): string
    {
        return 'email';
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function getFullPattern(): string
    {
        return $this->fullPattern;
    }

    public function getIdnEmailPattern(): string
    {
        return $this->idnEmailPattern;
    }

    public function isAllowName(): bool
    {
        return $this->allowName;
    }

    public function isCheckDNS(): bool
    {
        return $this->checkDNS;
    }

    public function isEnableIDN(): bool
    {
        return $this->enableIDN;
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
            'pattern' => $this->pattern,
            'fullPattern' => $this->fullPattern,
            'idnEmailPattern' => $this->idnEmailPattern,
            'allowName' => $this->allowName,
            'checkDNS' => $this->checkDNS,
            'enableIDN' => $this->enableIDN,
            'incorrectInputMessage' => [
                'message' => $this->incorrectInputMessage,
            ],
            'message' => [
                'message' => $this->message,
            ],
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ];
    }

    public function getHandlerClassName(): string
    {
        return EmailHandler::class;
    }
}
