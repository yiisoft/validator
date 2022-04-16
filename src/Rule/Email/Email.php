<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Email;

use Attribute;
use Closure;
use RuntimeException;
use Yiisoft\Validator\Rule\RuleNameTrait;
use Yiisoft\Validator\Rule\HandlerClassNameTrait;
use Yiisoft\Validator\RuleInterface;
use function function_exists;

/**
 * Validates that the value is a valid email address.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Email implements RuleInterface
{
    use RuleNameTrait;
    use HandlerClassNameTrait;

    public function __construct(
        /**
         * @var string the regular expression used to validate value.
         *
         * @link http://www.regular-expressions.info/email.html
         */
        public string $pattern = '/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/',
        /**
         * @var string the regular expression used to validate email addresses with the name part. This property is used
         * only when {@see $allowName} is `true`.
         *
         * @see $allowName
         */
        public string $fullPattern = '/^[^@]*<[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?>$/',
        /**
         * @var string the regular expression used to validate complex emails when {@see $enableIDN} is `true`.
         */
        public string $idnEmailPattern = '/^([a-zA-Z0-9._%+-]+)@((\[\d{1,3}\.\d{1,3}\.\d{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|\d{1,3})(\]?)$/',
        /**
         * @var bool whether to allow name in the email address (e.g. "John Smith <john.smith@example.com>"). Defaults
         * to `false`.
         *
         * @see $fullPattern
         */
        public bool $allowName = false,
        /**
         * @var bool whether to check whether the email's domain exists and has either an A or MX record.
         * Be aware that this check can fail due to temporary DNS problems even if the email address is
         * valid and an email would be deliverable. Defaults to `false`.
         */
        public bool $checkDNS = false,
        /**
         * @var bool whether validation process should take into account IDN (internationalized domain
         * names). Defaults to false meaning that validation of emails containing IDN will always fail.
         * Note that in order to use IDN validation you have to install and enable `intl` PHP extension,
         * otherwise an exception would be thrown.
         */
        public bool $enableIDN = false,
        public string $message = 'This value is not a valid email address.',
        public bool $skipOnEmpty = false,
        public bool $skipOnError = false,
        public ?Closure $when = null,
    ) {
        if ($enableIDN && !function_exists('idn_to_ascii')) {
            throw new RuntimeException('In order to use IDN validation intl extension must be installed and enabled.');
        }
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
            'message' => [
                'message' => $this->message,
            ],
            'skipOnEmpty' => $this->skipOnEmpty,
            'skipOnError' => $this->skipOnError,
        ];
    }
}
