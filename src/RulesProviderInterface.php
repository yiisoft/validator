<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Provides data validation rules.
 */
interface RulesProviderInterface
{
    /**
     * @return iterable A set of validation rules.
     */
    public function getRules(): iterable;
}
