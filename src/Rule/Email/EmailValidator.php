<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Email;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\ValidatorInterface;
use function is_string;
use function strlen;
use Yiisoft\Validator\Exception\UnexpectedRuleException;

/**
 * Validates that the value is a valid email address.
 */
final class EmailValidator implements RuleValidatorInterface
{
    public function validate(mixed $value, object $rule, ValidatorInterface $validator, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof Email) {
            throw new UnexpectedRuleException(Email::class, $rule);
        }

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
            if ($rule->enableIDN) {
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
                $valid = preg_match($rule->pattern, $value) || ($rule->allowName && preg_match(
                    $rule->fullPattern,
                    $value
                ));
                if ($valid && $rule->checkDNS) {
                    $valid = checkdnsrr($matches['domain'] . '.', 'MX') || checkdnsrr($matches['domain'] . '.', 'A');
                }
            }
        }

        if ($rule->enableIDN && $valid === false) {
            $valid = (bool) preg_match($rule->idnEmailPattern, $originalValue);
        }

        if ($valid === false) {
            $result->addError($rule->message);
        }

        return $result;
    }

    private function idnToAscii($idn)
    {
        return idn_to_ascii($idn, 0, INTL_IDNA_VARIANT_UTS46);
    }
}
