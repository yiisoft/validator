<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Closure;

/**
 * Validator validates {@link DataSetInterface} against rules set for data set attributes.
 *
 * @psalm-type RulesType = null|class-string|object|iterable<RuleInterface|RuleInterface[]|Closure|Closure[]>
 */
interface ValidatorInterface
{
    /**
     * Validate data set against rules set for data set attributes.
     *
     * @param DataSetInterface|mixed|RulesProviderInterface $data Data set to validate. If {@see RulesProviderInterface}
     * instance provided and rules are not specified explicitly, they are read from the
     * {@see RulesProviderInterface::getRules()}.
     * @param iterable|object|string|null $rules Rules to apply. If specified, rules are not read from data set even if
     * it is an instance of {@see RulesProviderInterface}.
     * @param ValidationContext|null $context Validation context that may take into account when performing validation.
     *
     * @psalm-param RulesType $rules
     */
    public function validate(
        mixed $data,
        iterable|object|string|null $rules = null,
        ?ValidationContext $context = null
    ): Result;
}
