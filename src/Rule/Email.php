<?php
namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;

/**
 * EmailValidator validates that the attribute value is a valid email address.
 */
class Email extends Rule
{
    /**
     * @var string the regular expression used to validateValue the attribute value.
     * @see http://www.regular-expressions.info/email.html
     */
    private $pattern = '/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/';
    /**
     * @var string the regular expression used to validateValue email addresses with the name part.
     * This property is used only when [[allowName]] is true.
     * @see allowName
     */
    private $fullPattern = '/^[^@]*<[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?>$/';
    /**
     * @var bool whether to allow name in the email address (e.g. "John Smith <john.smith@example.com>"). Defaults to false.
     * @see fullPattern
     */
    private $allowName = false;
    /**
     * @var bool whether to check whether the email's domain exists and has either an A or MX record.
     * Be aware that this check can fail due to temporary DNS problems even if the email address is
     * valid and an email would be deliverable. Defaults to false.
     */
    private $checkDNS = false;
    /**
     * @var bool whether validation process should take into account IDN (internationalized domain
     * names). Defaults to false meaning that validation of emails containing IDN will always fail.
     * Note that in order to use IDN validation you have to install and enable `intl` PHP extension,
     * otherwise an exception would be thrown.
     */
    private $enableIDN = false;


    private $message;

    public function __construct()
    {
        if ($this->enableIDN && !function_exists('idn_to_ascii')) {
            throw new \RuntimeException('In order to use IDN validation intl extension must be installed and enabled.');
        }
        if ($this->message === null) {
            $this->message = $this->formatMessage('{attribute} is not a valid email address.');
        }
    }

    public function validateValue($value): Result
    {
        $result = new Result();

        if (!is_string($value)) {
            $valid = false;
        } elseif (!preg_match('/^(?P<name>(?:"?([^"]*)"?\s)?)(?:\s+)?(?:(?P<open><?)((?P<local>.+)@(?P<domain>[^>]+))(?P<close>>?))$/i', $value, $matches)) {
            $valid = false;
        } else {
            if ($this->enableIDN) {
                $matches['local'] = $this->idnToAscii($matches['local']);
                $matches['domain'] = $this->idnToAscii($matches['domain']);
                $value = $matches['name'] . $matches['open'] . $matches['local'] . '@' . $matches['domain'] . $matches['close'];
            }

            if (strlen($matches['local']) > 64) {
                // The maximum total length of a user name or other local-part is 64 octets. RFC 5322 section 4.5.3.1.1
                // http://tools.ietf.org/html/rfc5321#section-4.5.3.1.1
                $valid = false;
            } elseif (strlen($matches['local'] . '@' . $matches['domain']) > 254) {
                // There is a restriction in RFC 2821 on the length of an address in MAIL and RCPT commands
                // of 254 characters. Since addresses that do not fit in those fields are not normally useful, the
                // upper limit on address lengths should normally be considered to be 254.
                //
                // Dominic Sayers, RFC 3696 erratum 1690
                // http://www.rfc-editor.org/errata_search.php?eid=1690
                $valid = false;
            } else {
                $valid = preg_match($this->pattern, $value) || $this->allowName && preg_match($this->fullPattern, $value);
                if ($valid && $this->checkDNS) {
                    $valid = checkdnsrr($matches['domain'] . '.', 'MX') || checkdnsrr($matches['domain'] . '.', 'A');
                }
            }
        }


        if ($valid === false) {
            $result->addError($this->message);
        }

        return $result;
    }

    private function idnToAscii($idn)
    {
        return idn_to_ascii($idn, 0, INTL_IDNA_VARIANT_UTS46);
    }

    /**
     * @param bool $allowName
     *
     * @return Email
     */
    public function allowName(bool $allowName): self
    {
        $this->allowName = $allowName;

        return $this;
    }

    /**
     * @param bool $checkDNS
     *
     * @return Email
     */
    public function checkDNS(bool $checkDNS): self
    {
        $this->checkDNS = $checkDNS;

        return $this;
    }

    /**
     * @param bool $enableIDN
     *
     * @return Email
     */
    public function enableIDN(bool $enableIDN): self
    {
        $this->enableIDN = $enableIDN;

        return $this;
    }
}
