<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\EmptyCondition\WhenEmpty;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleHandlerResolver\SimpleRuleHandlerContainer;
use Yiisoft\Validator\ValidationContext;

/**
 * A handler for {@see Required} rule. Validates that the specified value is passed and is not empty.
 *
 * @psalm-import-type EmptyCriteriaType from Required
 */
final class RequiredHandler implements RuleHandlerInterface
{
    /**
     * @var callable An empty criteria (either a callable or class implementing `__invoke()` method) used to
     * determine emptiness of the value. The signature must be like the following:
     *
     * ```php
     * function (mixed $value, bool $isAttributeMissing): bool
     * ```
     *
     * `$isAttributeMissing` is a flag defining whether the attribute is missing (not used / not passed at all).
     *
     * Used as a default when {@see Required::$emptyCriteria} is not set. A customized handler can be added to
     * {@see SimpleRuleHandlerContainer::$instances} to be applied to all rules of this type without explicitly
     * specifying empty criteria for each one of them.
     *
     * @psalm-var EmptyCriteriaType
     */
    private $defaultEmptyCondition;

    /**
     * @param callable|null $defaultEmptyCondition A default empty criteria used to determine emptiness of the value.
     * @psalm-param EmptyCriteriaType|null $defaultEmptyCondition
     */
    public function __construct(
        callable|null $defaultEmptyCondition = null,
    ) {
        $this->defaultEmptyCondition = $defaultEmptyCondition ?? new WhenEmpty(trimString: true);
    }

    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Required) {
            throw new UnexpectedRuleException(Required::class, $rule);
        }

        $result = new Result();
        if ($context->isAttributeMissing()) {
            $result->addError($rule->getNotPassedMessage(), ['attribute' => $context->getTranslatedAttribute()]);

            return $result;
        }

        $emptyCondition = $rule->getEmptyCondition() ?? $this->defaultEmptyCondition;

        if (!$emptyCondition($value, $context->isAttributeMissing())) {
            return $result;
        }

        $result->addError($rule->getMessage(), ['attribute' => $context->getTranslatedAttribute()]);

        return $result;
    }
}
