<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Countable;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Trait\LimitHandlerTrait;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

use function count;

/**
 * Validates that the value contains certain number of items. Can be applied to arrays or classes implementing
 * {@see Countable} interface.
 */
final class CountHandler implements RuleHandlerInterface
{
    use LimitHandlerTrait;

    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Count) {
            throw new UnexpectedRuleException(Count::class, $rule);
        }

        $result = new Result();

        if (!is_countable($value)) {
            $result->addError(
                message: $rule->getMessage(),
                parameters: ['attribute' => $context->getAttribute(), 'value' => $value]
            );

            return $result;
        }

        $count = count($value);
        $this->validateLimits($value, $rule, $context, $count, $result);

        return $result;
    }
}
