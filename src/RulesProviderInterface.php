<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Provides data validation rules.
 *
 * @psalm-type RawRulesIterable = iterable<RuleInterface|iterable<RuleInterface>|callable|iterable<callable>>
 */
interface RulesProviderInterface
{
    /**
     * @return iterable A set of validation rules.
     * @psalm-return RawRulesIterable
     */
    public function getRules(): iterable;
}
