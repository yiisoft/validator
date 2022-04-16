<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Json;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\ValidatorInterface;
use function is_string;
use Yiisoft\Validator\Exception\UnexpectedRuleException;

/**
 * Validates that the value is a valid json.
 */
final class JsonHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidatorInterface $validator, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof Json) {
            throw new UnexpectedRuleException(Json::class, $rule);
        }

        $result = new Result();

        if (!$this->isValidJson($value)) {
            $result->addError($rule->message);
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
}
