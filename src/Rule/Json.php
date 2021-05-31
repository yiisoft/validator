<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\HasValidationErrorMessage;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\ValidationContext;

use function is_string;

/**
 * JsonValidator validates that the attribute value is a valid json
 */
class Json extends Rule
{
    use HasValidationErrorMessage;

    private string $message = 'The value is not JSON.';

    public static function rule(): self
    {
        return new self();
    }

    protected function validateValue($value, ValidationContext $context = null): Result
    {
        $result = new Result();

        if (!$this->isValidJson($value)) {
            $result->addError($this->formatMessage($this->message));
        }

        return $result;
    }

    private function isValidJson($value): bool
    {
        // Regular expression is built based on JSON grammar specified at
        // https://tools.ietf.org/html/rfc8259
        $regex = <<<'REGEX'
        /
        (?(DEFINE)
            (?<json>(?>\s*(?&object)\s*|\s*(?&array)\s*))
            (?<object>(?>\{\s*(?>(?&member)(?>\s*,\s*(?&member))*)?\s*\}))
            (?<member>(?>(?&string)\s*:\s*(?&value)))
            (?<array>(?>\[\s*(?>(?&value)(?>\s*,\s*(?&value))*)?\s*\]))
            (?<value>(?>)false|null|true|(?&object)|(?&array)|(?&number)|(?&string))
            (?<number>(?>-?(?>0|[1-9]\d*)(?>\.\d+)?(?>[eE][-+]?\d+)?))
            (?<string>(?>"(?>\\(?>["\\\/bfnrt]|u[a-fA-F0-9]{4})|[^"\\\0-\x1F\x7F]+)*"))
        )
        \A(?&json)\z
        /x
        REGEX;

        return is_string($value) && preg_match($regex, $value) === 1;
    }

    public function getOptions(): array
    {
        return array_merge(
            parent::getOptions(),
            [
                'message' => $this->formatMessage($this->message),
            ],
        );
    }
}
