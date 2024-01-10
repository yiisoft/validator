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
 * Validates that the value is a valid HTTP or HTTPS URL.
 *
 * @see Url
 */
final class UrlHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Url) {
            throw new UnexpectedRuleException(Url::class, $rule);
        }

        $result = new Result();
        if (!is_string($value)) {
            $result->addError($rule->getIncorrectInputMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'Attribute' => ucfirst($context->getTranslatedAttribute()),
                'type' => get_debug_type($value),
            ]);

            return $result;
        }

        // Make sure the length is limited to avoid DOS attacks.
        if (strlen($value) < 2000) {
            if ($rule->isIdnEnabled()) {
                $value = $this->convertIdn($value);
            }

            if (preg_match($rule->getPattern(), $value)) {
                return $result;
            }
        }

        $result->addError($rule->getMessage(), [
            'attribute' => $context->getTranslatedAttribute(),
            'Attribute' => ucfirst($context->getTranslatedAttribute()),
            'value' => $value,
        ]);

        return $result;
    }

    /**
     * Encodes IDN domain name into its ASCII representation.
     *
     * @param string $idn IDN to convert.
     *
     * @return string Resulting ASCII string.
     *
     * @see idn_to_ascii()
     */
    private function idnToAscii(string $idn): string
    {
        $result = idn_to_ascii($idn);

        return $result === false ? '' : $result;
    }

    /**
     * Encodes either standalone IDN domain name or a domain name in a URL
     * into its ASCII representation.
     *
     * @param string $value IDN or URL to convert.
     *
     * @return string Resulting ASCII string.
     */
    private function convertIdn(string $value): string
    {
        if (!str_contains($value, '://')) {
            return $this->idnToAscii($value);
        }

        return preg_replace_callback(
            '/:\/\/([^\/]+)/',
            fn ($matches) => '://' . $this->idnToAscii($matches[1]),
            $value
        );
    }
}
