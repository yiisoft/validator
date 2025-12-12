<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\EmptyCondition\WhenEmpty;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleHandlerResolver\SimpleRuleHandlerContainer;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * A handler for {@see Required} rule. Validates that the specified value is passed and is not empty.
 *
 * @psalm-import-type EmptyConditionType from Required
 */
final class RequiredHandler implements RuleHandlerInterface
{
    /**
     * @var callable An empty condition (either a callable or class implementing `__invoke()` method) used to
     * determine emptiness of the value. The signature must be like the following:
     *
     * ```php
     * function (mixed $value, bool $isPropertyMissing): bool
     * ```
     *
     * `$isPropertyMissing` is a flag defining whether the property is missing (not used / not passed at all).
     *
     * Used as a default when {@see Required::$emptyCondition} is not set. A customized handler can be added to
     * {@see SimpleRuleHandlerContainer::$instances} to be applied to all rules of this type without explicitly
     * specifying empty condition for each one of them.
     *
     * @psalm-var EmptyConditionType
     */
    private $defaultEmptyCondition;

    /**
     * @param callable|null $defaultEmptyCondition A default empty condition used to determine emptiness of the value.
     *
     * @psalm-param EmptyConditionType|null $defaultEmptyCondition
     */
    public function __construct(
        ?callable $defaultEmptyCondition = null,
    ) {
        $this->defaultEmptyCondition = $defaultEmptyCondition ?? new WhenEmpty(trimString: true);
    }

    public function validate(mixed $value, RuleInterface $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Required) {
            throw new UnexpectedRuleException(Required::class, $rule);
        }

        $result = new Result();
        if ($context->isPropertyMissing()) {
            $result->addError($rule->getNotPassedMessage(), [
                'property' => $context->getTranslatedProperty(),
                'Property' => $context->getCapitalizedTranslatedProperty(),
            ]);

            return $result;
        }

        $emptyCondition = $rule->getEmptyCondition() ?? $this->defaultEmptyCondition;

        if (!$emptyCondition($value, $context->isPropertyMissing())) {
            return $result;
        }

        $result->addError($rule->getMessage(), [
            'property' => $context->getTranslatedProperty(),
            'Property' => $context->getCapitalizedTranslatedProperty(),
        ]);

        return $result;
    }
}
