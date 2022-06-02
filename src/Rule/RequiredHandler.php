<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Trait\EmptyCheckTrait;
use Yiisoft\Validator\Rule\Trait\FormatMessageTrait;
use Yiisoft\Validator\ValidationContext;
use function is_string;
use Yiisoft\Validator\Exception\UnexpectedRuleException;

/**
 * Validates that the specified value is neither null nor empty.
 */
final class RequiredHandler implements RuleHandlerInterface
{
    use EmptyCheckTrait;
    use FormatMessageTrait;

    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof Required) {
            throw new UnexpectedRuleException(Required::class, $rule);
        }

        $result = new Result();

        if ($this->isEmpty(is_string($value) ? trim($value) : $value)) {
            $formattedMessage = $this->formatMessage(
                $rule->getMessage(),
                ['attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($formattedMessage);
        }

        return $result;
    }
}
