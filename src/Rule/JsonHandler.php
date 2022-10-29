<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

use function is_string;

/**
 * Validates that the value is a valid json.
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
            $result->addError($rule->getMessage(), [
                'attribute' => $context->getAttribute(),
                'valueType' => get_debug_type($value),
            ]);

            return $result;
        }


        if (!$this->isValidJson($value)) {
            $result->addError($rule->getMessage(), [
                'attribute' => $context->getAttribute(),
                'value' => $value,
            ]);
        }

        return $result;
    }

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
