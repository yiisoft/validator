<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

use function is_string;
use function strlen;

/**
 * Validates that the value is a valid email address.
 */
final class EmailHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
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
            if ($rule->isEnableIDN()) {
                $matches['local'] = idn_to_ascii($matches['local']);
                $matches['domain'] = idn_to_ascii($matches['domain']);
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
                $valid = preg_match($rule->getPattern(), $value) || ($rule->isAllowName() && preg_match(
                    $rule->getFullPattern(),
                    $value
                ));
                if ($valid && $rule->isCheckDNS()) {
                    $valid = checkdnsrr($matches['domain'] . '.') || checkdnsrr($matches['domain'] . '.', 'A');
                }
            }
        }

        if ($valid === false && $rule->isEnableIDN()) {
            $valid = (bool) preg_match($rule->getIdnEmailPattern(), $originalValue);
        }

        if ($valid === false) {
            $result->addError(
                $rule->getMessage(),
                [
                    'attribute' => $context->getAttribute(),
                    'value' => $originalValue,
                ]
            );
        }

        return $result;
    }
}
