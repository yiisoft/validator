<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Allows to have data validation rules together with the data itself.
 * Such object can be passed as an only argument to the {@see ValidatorInterface}.
 */
interface RulesProviderInterface extends DataSetInterface
{
    /**
     * @return iterable A set of validation rules.
     */
    public function getRules(): iterable;
}
