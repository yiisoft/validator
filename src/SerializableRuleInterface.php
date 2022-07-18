<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * The interface should be implemented for a rule that is meant to be converted to array of name => parameters.
 * Such array is usually passed to the client and then used for client-side validation.
 */
interface SerializableRuleInterface extends RuleInterface
{
    /**
     * Returns rule options as array.
     *
     * @return array
     */
    public function getOptions(): array;
}
