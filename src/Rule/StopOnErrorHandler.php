<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use InvalidArgumentException;
use Yiisoft\Translator\TranslatorInterface;
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
 *      new ExistInDatabase(), // Heavy operation. It would be great not to call it if the previous rule was failed.
 *     )];
 * ]);
 * ```
 */
final class StopOnErrorHandler implements RuleHandlerInterface
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof StopOnError) {
            throw new UnexpectedRuleException(StopOnError::class, $rule);
        }

        if ($rule->getRules() === []) {
            throw new InvalidArgumentException(
                'Rules for StopOnError rule are required.'
            );
        }

        $compoundResult = new Result();
        $results = [];

        foreach ($rule->getRules() as $rule) {
            $rules = [$rule];

            if (is_iterable($rule)) {
                $rules = [new StopOnError($rule)];
            }

            $lastResult = $context->getValidator()->validate($value, $rules);
            $results[] = $lastResult;

            if (!$lastResult->isValid()) {
                break;
            }
        }

        foreach ($results as $result) {
            foreach ($result->getErrors() as $error) {
                $compoundResult->addError($error->getMessage(), $error->getValuePath());
            }
        }

        return $compoundResult;
    }
}
