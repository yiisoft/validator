<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Required;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\AtLeast\AtLeast;
use Yiisoft\Validator\Rule\EmptyCheckTrait;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\ValidatorInterface;
use function is_string;
use Yiisoft\Validator\Exception\UnexpectedRuleException;

/**
 * Validates that the specified value is neither null nor empty.
 */
final class RequiredValidator implements RuleValidatorInterface
{
    use EmptyCheckTrait;

    public function validate(mixed $value, object $rule, ValidatorInterface $validator, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof Required) {
            throw new UnexpectedRuleException(Required::class, $rule);
        }

        $result = new Result();

        if ($this->isEmpty(is_string($value) ? trim($value) : $value)) {
            $result->addError($rule->message);
        }

        return $result;
    }
}
