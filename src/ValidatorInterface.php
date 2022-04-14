<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Validator validates {@link DataSetInterface} against rules set for data set attributes.
 */
interface ValidatorInterface
{
    /**
     * Validate data set against rules set for data set attributes.
     *
     * @param DataSetInterface|mixed|RulesProviderInterface $data Data set to validate.
     * @param RuleInterface[][]|RuleInterface[] $rules Rules to apply.
     * @psalm-param iterable<string, RuleInterface[]> $rules
     */
    public function validate(mixed $data, iterable $rules = []): Result;
}
