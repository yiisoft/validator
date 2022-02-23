<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use RuntimeException;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\ValidationContext;

use function function_exists;
use function is_string;
use function strlen;

/**
 * EmailValidator validates that the attribute value is a valid email address.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Email extends Rule
{
    public function __construct(
        /**
         * @var string the regular expression used to validate value.
         *
         * @link http://www.regular-expressions.info/email.html
         */
        private string $pattern = '/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/',
        /**
         * @var string the regular expression used to validate email addresses with the name part. This property is used
         * only when {@see $allowName} is `true`.
         *
         * @see $allowName
         */
        private string $fullPattern = '/^[^@]*<[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?>$/',
        /**
         * @var string the regular expression used to validate complex emails when idn is enabled.
         */
        private string $patternIdnEmail = '/^([a-zA-Z0-9._%+-]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/',
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
        private string $message = 'This value is not a valid email address.',
        ?FormatterInterface $formatter = null,
        bool $skipOnEmpty = false,
        bool $skipOnError = false,
        $when = null
    ) {
        parent::__construct(formatter: $formatter, skipOnEmpty: $skipOnEmpty, skipOnError: $skipOnError, when: $when);

        if ($enableIDN && !function_exists('idn_to_ascii')) {
            throw new RuntimeException('In order to use IDN validation intl extension must be installed and enabled.');
        }
    }

    protected function validateValue($value, ?ValidationContext $context = null): Result
    {
        $originalValue = $value;
        $result = new Result();

        if (!is_string($value)) {
            $valid = false;
        } elseif (!preg_match(
            '/^(?P<name>(?:"?([^"]*)"?\s)?)(?:\s+)?((?P<open><?)((?P<local>.+)@(?P<domain>[^>]+))(?P<close>>?))$/i',
            $value,
            $matches
        )) {
            $valid = false;
        } else {
            /** @psalm-var array{name:string,local:string,open:string,domain:string,close:string} $matches */
            if ($this->enableIDN) {
                $matches['local'] = $this->idnToAscii($matches['local']);
                $matches['domain'] = $this->idnToAscii($matches['domain']);
                $value = implode([
                    $matches['name'],
                    $matches['open'],
                    $matches['local'],
                    '@',
                    $matches['domain'],
                    $matches['close'],
                ]);
            }

            if (is_string($matches['local']) && strlen($matches['local']) > 64) {
                // The maximum total length of a user name or other local-part is 64 octets. RFC 5322 section 4.5.3.1.1
                // http://tools.ietf.org/html/rfc5321#section-4.5.3.1.1
                $valid = false;
            } elseif (is_string($matches['local']) && strlen($matches['local'] . '@' . $matches['domain']) > 254) {
                // There is a restriction in RFC 2821 on the length of an address in MAIL and RCPT commands
                // of 254 characters. Since addresses that do not fit in those fields are not normally useful, the
                // upper limit on address lengths should normally be considered to be 254.
                //
                // Dominic Sayers, RFC 3696 erratum 1690
                // http://www.rfc-editor.org/errata_search.php?eid=1690
                $valid = false;
            } else {
                $valid = preg_match($this->pattern, $value) || ($this->allowName && preg_match(
                    $this->fullPattern,
                    $value
                ));
                if ($valid && $this->checkDNS) {
                    $valid = checkdnsrr($matches['domain'] . '.', 'MX') || checkdnsrr($matches['domain'] . '.', 'A');
                }
            }
        }

        if ($this->enableIDN && $valid === false) {
            $valid = (bool) preg_match($this->patternIdnEmail, $originalValue);
        }

        if ($valid === false) {
            $result->addError($this->formatMessage($this->message));
        }

        return $result;
    }

    private function idnToAscii($idn)
    {
        return idn_to_ascii($idn, 0, INTL_IDNA_VARIANT_UTS46);
    }

    public function getOptions(): array
    {
        return array_merge(parent::getOptions(), [
            'allowName' => $this->allowName,
            'checkDNS' => $this->checkDNS,
            'enableIDN' => $this->enableIDN,
            'message' => $this->formatMessage($this->message),
        ]);
    }
}
