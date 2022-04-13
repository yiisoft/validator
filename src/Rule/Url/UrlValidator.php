<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Url;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\ValidationContext;
use function is_string;
use function strlen;

/**
 * Validates that the value is a valid HTTP or HTTPS URL.
 *
 * Note that this rule only checks if the URL scheme and host part are correct.
 * It does not check the remaining parts of a URL.
 */
final class UrlValidator implements RuleValidatorInterface
{
    public static function getConfigClassName(): string
    {
        return Url::class;
    }

    public function validate(mixed $value, object $config, ?ValidationContext $context = null): Result
    {
        $result = new Result();

        // make sure the length is limited to avoid DOS attacks
        if (is_string($value) && strlen($value) < 2000) {
            if ($config->enableIDN) {
                $value = $this->convertIdn($value);
            }

            if (preg_match($config->getPattern(), $value)) {
                return $result;
            }
        }

        $result->addError($config->message);

        return $result;
    }

    private function idnToAscii(string $idn): string
    {
        $result = idn_to_ascii($idn, 0, INTL_IDNA_VARIANT_UTS46);

        return $result === false ? '' : $result;
    }

    private function convertIdn(string $value): string
    {
        if (strpos($value, '://') === false) {
            return $this->idnToAscii($value);
        }

        return preg_replace_callback(
            '/:\/\/([^\/]+)/',
            fn ($matches) => '://' . $this->idnToAscii($matches[1]),
            $value
        );
    }
}
