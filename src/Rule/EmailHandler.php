<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\ValidationContext;

use function is_string;
use function strlen;

/**
 * Validates that the value is a valid email address.
 */
final class EmailHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, RuleInterface $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Email) {
            throw new UnexpectedRuleException(Email::class, $rule);
        }

        $result = new Result();
        if (!is_string($value)) {
            return $result->addError($rule->getIncorrectInputMessage(), [
                'property' => $context->getTranslatedProperty(),
                'Property' => $context->getCapitalizedTranslatedProperty(),
                'type' => get_debug_type($value),
            ]);
        }

        if (!preg_match(
            '/^(?P<name>(?:"?([^"]*)"?\s)?)(?:\s+)?((?P<open><?)((?P<local>.+)@(?P<domain>[^>]+))(?P<close>>?))/',
            $value,
            $matches,
        )) {
            $valid = $rule->isIdnEnabled()
                ? (bool) preg_match($rule->getIdnEmailPattern(), $value)
                : false;
        } else {
            /**
             * @psalm-var array{
             *     name: string,
             *     local: non-empty-string,
             *     open: string,
             *     domain: non-empty-string,
             *     close: string,
             * } $matches
             */
            $valid = $this->validateParsedValue(
                $rule,
                $value,
                $matches['name'],
                $matches['local'],
                $matches['open'],
                $matches['domain'],
                $matches['close'],
            );
        }

        if ($valid === false) {
            $result->addError($rule->getMessage(), [
                'property' => $context->getTranslatedProperty(),
                'Property' => $context->getCapitalizedTranslatedProperty(),
                'value' => $value,
            ]);
        }

        return $result;
    }

    /**
     * @param non-empty-string $local
     * @param non-empty-string $domain
     */
    private function validateParsedValue(
        Email $rule,
        string $value,
        string $name,
        string $local,
        string $open,
        string $domain,
        string $close,
    ): bool {
        if ($rule->isIdnEnabled()) {
            $originalLocal = $local;
            $local = idn_to_ascii($local);
            $originalDomain = $domain;
            $domain = idn_to_ascii($domain);
            $value = implode('', [
                $name,
                $open,
                $local == false ? $originalLocal : $local,
                '@',
                $domain == false ? $originalDomain : $domain,
                $close,
            ]);
        }

        if (is_string($local) && strlen($local) > 64) {
            // The maximum total length of a user name or other local-part is 64 octets. RFC 5322 section 4.5.3.1.1
            // https://www.rfc-editor.org/rfc/rfc5321#section-4.5.3.1.1
            return false;
        }

        if (is_string($local) && is_string($domain) && (strlen($local) + strlen($domain) > 253)) {
            // There is a restriction in RFC 2821 on the length of an address in MAIL and RCPT commands
            // of 254 characters. Since addresses that do not fit in those fields are not normally useful, the
            // upper limit on address lengths should normally be considered to be 254.
            //
            // Dominic Sayers, RFC 3696 erratum 1690
            // https://www.rfc-editor.org/errata_search.php?eid=1690
            return false;
        }

        $valid = preg_match($rule->getPattern(), $value)
            || (
                $rule->isNameAllowed()
                && preg_match($rule->getFullPattern(), $value)
            );
        if ($valid && $rule->shouldCheckDns()) {
            $valid = is_string($domain) && checkdnsrr($domain);
        }

        return $valid;
    }
}
