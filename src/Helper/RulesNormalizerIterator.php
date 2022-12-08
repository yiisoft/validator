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
 * @internal
 *
 * @template-implements IteratorAggregate<int, RuleInterface>
 */
final class RulesNormalizerIterator implements IteratorAggregate
{
    private $defaultSkipOnEmptyCriteria;

    public function __construct(
        private iterable $rules,
        ?callable $defaultSkipOnEmptyCriteria = null
    ) {
        $this->defaultSkipOnEmptyCriteria = $defaultSkipOnEmptyCriteria;
    }

    public function getIterator(): Traversable
    {
        /** @var mixed $rule */
        foreach ($this->rules as $rule) {
            yield self::normalizeRule($rule, $this->defaultSkipOnEmptyCriteria);
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    private static function normalizeRule(mixed $rule, ?callable $defaultSkipOnEmptyCriteria): RuleInterface
    {
        if (is_callable($rule)) {
            return new Callback($rule);
        }

        if (!$rule instanceof RuleInterface) {
            throw new InvalidArgumentException(
                sprintf(
                    'Rule should be either an instance of %s or a callable, %s given.',
                    RuleInterface::class,
                    get_debug_type($rule)
                )
            );
        }

        if (
            $defaultSkipOnEmptyCriteria !== null
            && $rule instanceof SkipOnEmptyInterface
            && $rule->getSkipOnEmpty() === null
        ) {
            $rule = $rule->skipOnEmpty($defaultSkipOnEmptyCriteria);
        }

        return $rule;
    }
}
