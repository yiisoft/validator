<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Validator\Helper\DataSetNormalizer;
use Yiisoft\Validator\Helper\RulesNormalizer;

/**
 * An interface allowing to validate the data using the set of rules ({@see RuleInterface}) and validation context
 * ({@see ValidationContext}). A class implementing it called "validator".
 *
 * @psalm-type RulesType = null|class-string|object|callable|iterable<RuleInterface|RuleInterface[]|callable|callable[]>
 */
interface ValidatorInterface
{
    /**
     * Validates the data using the set of rules ({@see RuleInterface}) and validation context
     * ({@see ValidationContext}).
     *
     * @param DataSetInterface|mixed|RulesProviderInterface $data Data to validate:
     *
     * - A data set ({@see DataSetInterface}) is used as is.
     * - Implementing {@see RulesProviderInterface} additionally can be used for providing rules via
     * {@see RulesProviderInterface::getRules()} (works only when `$rules` argument is not provided and ignored
     * otherwise).
     * - Any other value is normalized to data set using {@see DataSetNormalizer}.
     * @param callable|iterable|object|string|null $rules Rules to apply for validating data. If specified, have higher
     * priority over {@see RulesProviderInterface::getRules()} provided in `$data` argument. A variety of types are
     * supported, they are normalized with {@see RulesNormalizer} before using.
     * @param ValidationContext|null $context Validation context that may be taken into account when performing
     * validation.
     *
     * @psalm-param RulesType $rules
     */
    public function validate(
        mixed $data,
        callable|iterable|object|string|null $rules = null,
        ?ValidationContext $context = null,
    ): Result;
}
