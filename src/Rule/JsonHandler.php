<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\ValidationContext;

use function function_exists;
use function is_string;

/**
 * A handler for {@see Json} rule. Validates that the value is a valid JSON string.
 *
 * @see https://en.wikipedia.org/wiki/JSON
 * @see https://tools.ietf.org/html/rfc8259
 */
final class JsonHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, RuleInterface $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Json) {
            throw new UnexpectedRuleException(Json::class, $rule);
        }

        $result = new Result();
        if (!is_string($value)) {
            return $result->addError($rule->getIncorrectInputMessage(), [
                'attribute' => $context->getTranslatedProperty(),
                'Attribute' => $context->getCapitalizedTranslatedProperty(),
                'type' => get_debug_type($value),
            ]);
        }


        if (!$this->isValidJson($value)) {
            return $result->addError($rule->getMessage(), [
                'attribute' => $context->getTranslatedProperty(),
                'Attribute' => $context->getCapitalizedTranslatedProperty(),
                'value' => $value,
            ]);
        }

        return $result;
    }

    /**
     * Checks if the given value is a valid JSON.
     *
     * @param string $value Any string.
     *
     * @return bool Whether the given value is a valid JSON: `true` - valid JSON, `false` - invalid JSON with errors /
     * not a JSON at all.
     */
    private function isValidJson(string $value): bool
    {
        if (function_exists('json_validate')) {
            /** @var bool Can be removed after upgrading to PHP 8.3 */
            return json_validate($value);
        }

        json_decode($value);

        return json_last_error() === JSON_ERROR_NONE;
    }
}
