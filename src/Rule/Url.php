<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\HasValidationErrorMessage;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\ValidationContext;

use function is_string;
use function strlen;

/**
 * UrlValidator validates that the attribute value is a valid HTTP or HTTPS URL.
 *
 * Note that this validator only checks if the URL scheme and host part are correct.
 * It does not check the remaining parts of a URL.
 */
class Url extends Rule
{
    use HasValidationErrorMessage;

    /**
     * @var string the regular expression used to validateValue the attribute value.
     * The pattern may contain a `{schemes}` token that will be replaced
     * by a regular expression which represents the {@see schemes()}.
     */
    private string $pattern = '/^{schemes}:\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(?::\d{1,5})?(?:$|[?\/#])/i';
    /**
     * @var array list of URI schemes which should be considered valid. By default, http and https
     * are considered to be valid schemes.
     */
    private array $validSchemes = ['http', 'https'];
    /**
     * @var bool whether validation process should take into account IDN (internationalized
     * domain names). Defaults to false meaning that validation of URLs containing IDN will always
     * fail. Note that in order to use IDN validation you have to install and enable `intl` PHP
     * extension, otherwise an exception would be thrown.
     */
    private bool $enableIDN = false;

    private string $message = 'This value is not a valid URL.';

    protected function validateValue($value, ValidationContext $context = null): Result
    {
        $result = new Result();

        // make sure the length is limited to avoid DOS attacks
        if (is_string($value) && strlen($value) < 2000) {
            if ($this->enableIDN) {
                $value = $this->convertIdn($value);
            }

            if (preg_match($this->getPattern(), $value)) {
                return $result;
            }
        }

        $result->addError($this->formatMessage($this->message));

        return $result;
    }

    private function idnToAscii($idn): string
    {
        $result = idn_to_ascii($idn, 0, INTL_IDNA_VARIANT_UTS46);

        return $result === false ? '' : $result;
    }

    private function convertIdn($value): string
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

    private function getPattern(): string
    {
        if (strpos($this->pattern, '{schemes}') !== false) {
            return str_replace('{schemes}', '(' . implode('|', $this->validSchemes) . ')', $this->pattern);
        }

        return $this->pattern;
    }

    public function pattern(string $pattern): self
    {
        $new = clone $this;
        $new->pattern = $pattern;
        return $new;
    }

    public function enableIDN(): self
    {
        if (!function_exists('idn_to_ascii')) {
            throw new \RuntimeException('In order to use IDN validation intl extension must be installed and enabled.');
        }

        $new = clone $this;
        $new->enableIDN = true;
        return $new;
    }

    public function schemes(array $schemes): self
    {
        $new = clone $this;
        $new->validSchemes = $schemes;
        return $new;
    }

    public function getOptions(): array
    {
        return array_merge(
            parent::getOptions(),
            [
                'message' => $this->formatMessage($this->message),
                'enableIDN' => $this->enableIDN,
                'validSchemes' => $this->validSchemes,
                'pattern' => $this->pattern,
            ],
        );
    }
}
