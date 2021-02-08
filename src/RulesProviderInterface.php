<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Allows pass only one argument to the {@see ValidatorInterface}.
 */
interface RulesProviderInterface extends DataSetInterface
{
    public function getRules(): iterable;
}
