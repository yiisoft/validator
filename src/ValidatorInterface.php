<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Closure;

/**
 * Validator validates {@link DataSetInterface} against rules set for data set attributes.
 */
interface ValidatorInterface
{
    /**
     * Validate data set against rules set for data set attributes.
     *
     * @param DataSetInterface|mixed|RulesProviderInterface $data Data set to validate. If {@see RulesProviderInterface}
     * instance provided and rules are not specified explicitly, they are read from the
     * {@see RulesProviderInterface::getRules()}.
     * @param iterable<Closure|Closure[]|RuleInterface|RuleInterface[]>|null $rules Rules to apply. If specified,
     * rules are not read from data set even if it is an instance of {@see RulesProviderInterface}.
     */
    public function validate(mixed $data, iterable|RulesProviderInterface|null $rules = null): Result;
}
