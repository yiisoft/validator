<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Provides data validation rules.
 *
 * @psalm-import-type RawRulesMap from ValidatorInterface
 */
interface RulesProviderInterface
{
    /**
     * @return iterable A set of validation rules.
     *
     * @psalm-return RawRulesMap
     */
    public function getRules(): iterable;
}
