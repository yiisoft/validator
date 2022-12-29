<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * Can be used for early stopping the validation process of the value when the validation on the stage was failed.
 *
 * For example, we have several rules, but we want not to process the rest rules when fail was ocurred:
 *
 * ```php
 * $request = [
 *     'username' => 'yiisoft',
 * ];
 * ```
 *
 * So to make validation we can configure it like this:
 *
 * ```php
 * $rule = new StopOnError([
 *      new HasLength(min: 3),
 *      // Heavy operation. It would be great not to call it if the previous rule was failed.
 *      new ExistsInDatabase(),
 * ]);
 * ```
 */
final class StopOnErrorHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof StopOnError) {
            throw new UnexpectedRuleException(StopOnError::class, $rule);
        }

        if ($context->getParameter(ValidationContext::PARAMETER_PREVIOUS_RULES_ERRORED) === true) {
            return new Result();
        }

        foreach ($rule->getRules() as $relatedRule) {
            $result = $context->validate($value, $relatedRule);
            if (!$result->isValid()) {
                return $result;
            }
        }

        return new Result();
    }
}
