<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Reflection;
use ReflectionException;
use Yiisoft\Validator\Helper\DataSetNormalizer;
use Yiisoft\Validator\Helper\RulesNormalizer;

/**
 * An interface allowing to validate the data according to the set of rules ({@see RuleInterface}) and validation
 * context ({@see ValidationContext}). A class implementing it is called "validator".
 *
 * @psalm-type RulesType = null|class-string|object|callable|iterable<RuleInterface|iterable<RuleInterface>|callable|iterable<callable>>
 */
interface ValidatorInterface
{
    /**
     * Validates the data according to the set of rules ({@see RuleInterface}) and validation context
     * ({@see ValidationContext}).
     *
     * @param DataSetInterface|mixed|RulesProviderInterface $data Data to validate:
     *
     * - A data set ({@see DataSetInterface}) is used as is.
     * - Implementing {@see RulesProviderInterface} additionally can be used for providing rules via
     * {@see RulesProviderInterface::getRules()} (works only when `$rules` argument is not provided and ignored
     * otherwise).
     * - Any other value is normalized to data set using {@see DataSetNormalizer}.
     * @param callable|iterable|object|string|null $rules Rules to apply for validating data. If specified, this
     * argument has higher priority over {@see RulesProviderInterface::getRules()} provided in `$data` argument. A
     * variety of types is supported. They are normalized before usage, please refer to {@see RulesNormalizer}
     * documentation to see what structures can be passed.
     * @psalm-param RulesType $rules
     *
     * @param ValidationContext|null $context Validation context that may be taken into account when performing
     * validation.
     *
     * @throws ReflectionException If an object / {@see ObjectDataSet} providing rules or (and) data used in `$data`
     * argument and there was a {@see Reflection} error during parsing them.
     *
     * @return Result The result of validation.
     */
    public function validate(
        mixed $data,
        callable|iterable|object|string|null $rules = null,
        ?ValidationContext $context = null,
    ): Result;
}
