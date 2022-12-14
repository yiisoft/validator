<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\EmptyCriteria\WhenEmpty;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * Validates that the specified value is passed and not empty.
 *
 * @psalm-import-type EmptyCriteriaType from Required
 */
final class RequiredHandler implements RuleHandlerInterface
{
    /**
     * @var callable
     * @psalm-var EmptyCriteriaType
     */
    private $defaultEmptyCriteria;

    /**
     * @psalm-param EmptyCriteriaType|null $defaultEmptyCriteria
     */
    public function __construct(
        callable|null $defaultEmptyCriteria = null,
    ) {
        $this->defaultEmptyCriteria = $defaultEmptyCriteria ?? new WhenEmpty(trimString: true);
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

        $emptyCriteria = $rule->getEmptyCriteria() ?? $this->defaultEmptyCriteria;

        if (!$emptyCriteria($value, $context->isAttributeMissing())) {
            return $result;
        }

        $result->addError($rule->getMessage(), ['attribute' => $context->getTranslatedAttribute()]);

        return $result;
    }
}
