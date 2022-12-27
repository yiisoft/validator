<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

use function is_string;

/**
 * A handler for {@see Json} rule. Validates that the value is a valid JSON string.
 *
 * @see https://en.wikipedia.org/wiki/JSON
 * @see https://tools.ietf.org/html/rfc8259
 */
final class JsonHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Json) {
            throw new UnexpectedRuleException(Json::class, $rule);
        }

        $result = new Result();
        if (!is_string($value)) {
            return $result->addError($rule->getIncorrectInputMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'type' => get_debug_type($value),
            ]);
        }


        if (!$this->isValidJson($value)) {
            return $result->addError($rule->getMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'value' => $value,
            ]);
        }

        return $result;
    }

    /**
     * Checks if the given value is a valid JSON.
     *
     * @param string $value Any string.
     * @return bool Whether the given value is a valid JSON: `true` - valid JSON, `false` - invalid JSON with errors /
     * not a JSON at all.
     */
    private function isValidJson(string $value): bool
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

        return preg_match($regex, $value) === 1;
    }
}
