<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * Can be used to group rules for validation by `skipOnEmpty`, `skipOnError` or `when`.
 *
 * For example, we have same when closure:
 * ```
 * 'name' => [
 *     new Required(when: fn() => $this->useName),
 *     new HasLength(min: 1, max: 50, skipOnEmpty: true, when: fn() => $this->useName),
 * ],
 * ```
 * So we can configure it like this:
 * ```
 * 'name' => [
 *     new Composite(
 *         rules: [
 *             new Required(),
 *             new HasLength(min: 1, max: 50, skipOnEmpty: true),
 *         ],
 *         when: fn() => $this->useName,
 *     ),
 * ],
 * ```
 */
final class CompositeHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Composite) {
            throw new UnexpectedRuleException(Composite::class, $rule);
        }

        return $context->validate($value, $rule->getRules());
    }
}
