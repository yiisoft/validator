<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Trait\FormatMessageTrait;
use Yiisoft\Validator\ValidationContext;
use function is_string;
use function strlen;
use Yiisoft\Validator\Exception\UnexpectedRuleException;

/**
 * Validates that the value is a valid HTTP or HTTPS URL.
 *
 * Note that this rule only checks if the URL scheme and host part are correct.
 * It does not check the remaining parts of a URL.
 */
final class UrlHandler implements RuleHandlerInterface
{
    use FormatMessageTrait;

    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof Url) {
            throw new UnexpectedRuleException(Url::class, $rule);
        }

        $result = new Result();

        // make sure the length is limited to avoid DOS attacks
        if (is_string($value) && strlen($value) < 2000) {
            if ($rule->isEnableIDN()) {
                $value = $this->convertIdn($value);
            }

            if (preg_match($rule->getPattern(), $value)) {
                return $result;
            }
        }

        $formattedMessage = $this->formatMessage(
            $rule->getMessage(),
            ['attribute' => $context?->getAttribute(), 'value' => $value]
        );
        $result->addError($formattedMessage);

        return $result;
    }

    private function idnToAscii(string $idn): string
    {
        $result = idn_to_ascii($idn, 0, INTL_IDNA_VARIANT_UTS46);

        return $result === false ? '' : $result;
    }

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
