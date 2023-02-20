<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Provides data validation rules.
 *
 * @psalm-import-type RawRulesIterable from ValidatorInterface
 */
interface RulesProviderInterface
{
    /**
     * @return iterable A set of validation rules.
     * @psalm-return RawRulesIterable
     */
    public function getRules(): iterable;
}
