<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Helper;

use InvalidArgumentException;
use IteratorAggregate;
use Traversable;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;

use function is_callable;

/**
 * An iterator for a set of rules, performs normalization for every individual rule unifying other provided types and
 * verifying that every item is a valid rule instance. It also applies some default settings if needed.
 *
 * @internal
 *
 * @template-implements IteratorAggregate<int, RuleInterface>
 */
final class RulesNormalizerIterator implements IteratorAggregate
{
    /**
     * @var callable|null A default "skip on empty" condition ({@see SkipOnEmptyInterface}), already normalized. Used to
     * optimize setting the same value in all the rules. Defaults to `null` meaning that it's not used.
     */
    private $defaultSkipOnEmptyCondition;

    /**
     * @param iterable $rules A rules' iterable for checking and normalization.
     * @param callable|null $defaultSkipOnEmptyCondition A default "skip on empty" condition
     * ({@see SkipOnEmptyInterface}), already normalized. Used to optimize setting the same value in all the rules.
     * Defaults to `null` meaning that it's not used.
     */
    public function __construct(
        private iterable $rules,
        ?callable $defaultSkipOnEmptyCondition = null,
    ) {
        $this->defaultSkipOnEmptyCondition = $defaultSkipOnEmptyCondition;
    }

    public function getIterator(): Traversable
    {
        /** @var mixed $rule */
        foreach ($this->rules as $rule) {
            yield self::normalizeRule($rule, $this->defaultSkipOnEmptyCondition);
        }
    }

    /**
     * Normalizes a single rule:
     *
     * - A callable is wrapped with {@see Callback} rule.
     * - For any other type verifies that it's a valid rule instance.
     * - If default "skip on empty" condition is set, applies it if possible.
     *
     * @param mixed $rule A raw rule.
     * @param callable|null $defaultSkipOnEmptyCondition A "skip on empty" condition ({@see SkipOnEmptyInterface}) to
     * apply as default, already normalized. `null` means there is no condition to apply.
     *
     * @throws InvalidArgumentException When rule is neither a callable nor a {@see RuleInterface} implementation.
     *
     * @return RuleInterface Ready to use rule instance.
     */
    private static function normalizeRule(mixed $rule, ?callable $defaultSkipOnEmptyCondition): RuleInterface
    {
        if (is_callable($rule)) {
            return new Callback($rule);
        }

        if (!$rule instanceof RuleInterface) {
            throw new InvalidArgumentException(
                sprintf(
                    'Rule must be either an instance of %s or a callable, %s given.',
                    RuleInterface::class,
                    get_debug_type($rule)
                )
            );
        }

        if (
            $defaultSkipOnEmptyCondition !== null
            && $rule instanceof SkipOnEmptyInterface
            && $rule->getSkipOnEmpty() === null
        ) {
            $rule = $rule->skipOnEmpty($defaultSkipOnEmptyCondition);
        }

        return $rule;
    }
}
