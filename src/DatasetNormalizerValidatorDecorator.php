<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Closure;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionProperty;
use Traversable;
use Yiisoft\Validator\RulesProvider\AttributesRulesProvider;

use function is_array;

/**
 * Validator validates {@link DataSetInterface} against rules set for data set attributes.
 */
final class DatasetNormalizerValidatorDecorator implements ValidatorInterface
{
    public function __construct(
        private ValidatorInterface $decorated,
        /**
         * @var int What visibility levels to use when reading rules from the class specified in `$rules` argument in
         * {@see validate()} method.
         */
        private int $rulesPropertyVisibility = ReflectionProperty::IS_PRIVATE
        | ReflectionProperty::IS_PROTECTED
        | ReflectionProperty::IS_PUBLIC,
    ) {
    }

    /**
     * @param DataSetInterface|mixed|RulesProviderInterface $data
     * @param class-string|iterable<Closure|Closure[]|RuleInterface|RuleInterface[]>|RulesProviderInterface|null $rules
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function validate(
        mixed $data,
        iterable|RulesProviderInterface|null $rules = null
    ): Result {
        if ($rules === null && $data instanceof RulesProviderInterface) {
            $rules = $data->getRules();
        } elseif ($rules instanceof RulesProviderInterface) {
            $rules = $rules->getRules();
        } elseif (!$rules instanceof Traversable && !is_array($rules) && $rules !== null) {
            $rules = (new AttributesRulesProvider($rules, $this->rulesPropertyVisibility))->getRules();
        }

        return $this->decorated->validate($data, $rules);
    }
}
