<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Closure;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use ReflectionException;
use ReflectionProperty;
use Traversable;
use Yiisoft\Validator\DataSet\ArrayDataSet;
use Yiisoft\Validator\DataSet\MixedDataSet;
use Yiisoft\Validator\DataSet\ObjectDataSet;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Trait\PreValidateTrait;

use Yiisoft\Validator\RulesProvider\AttributesRulesProvider;

use function is_callable;
use function is_int;

/**
 * Validator validates {@link DataSetInterface} against rules set for data set attributes.
 */
final class Validator implements ValidatorInterface
{
    use PreValidateTrait;

    /**
     * @var callable
     */
    private $defaultSkipOnEmptyCallback;

    public function __construct(
        private RuleHandlerResolverInterface $ruleHandlerResolver,

        /**
         * @var bool|callable|null
         */
        $defaultSkipOnEmpty = null,
        /**
         * @var int What visibility levels to use when reading rules from the class specified in `$rules` argument in
         * {@see validate()} method.
         */
        private int $rulesPropertyVisibility = ReflectionProperty::IS_PRIVATE
        | ReflectionProperty::IS_PROTECTED
        | ReflectionProperty::IS_PUBLIC,
    ) {
        $this->defaultSkipOnEmptyCallback = SkipOnEmptyNormalizer::normalize($defaultSkipOnEmpty);
    }

    /**
     * @param DataSetInterface|mixed|RulesProviderInterface $data
     * @param iterable|RulesProviderInterface|null $rules
     * @param ValidationContext|null $context
     *
     * @throws ReflectionException
     *
     * @return Result
     */
    public function validate(
        mixed $data,
        iterable|RulesProviderInterface|null $rules = null,
        ?ValidationContext $context = null,
    ): Result {
        $data = $this->normalizeDataSet($data);

        if ($rules === null && $data instanceof RulesProviderInterface) {
            $rules = $data->getRules();
        } elseif ($rules instanceof RulesProviderInterface) {
            $rules = $rules->getRules();
        } elseif (!$rules instanceof Traversable && !is_array($rules) && $rules !== null) {
            $rules = (new AttributesRulesProvider($rules, $this->rulesPropertyVisibility))->getRules();
        }

        $context = new ValidationContext(
            $context?->getValidator() ?? $this,
            $context?->getDataSet() ?? $data,
            $context?->getAttribute() ?? null,
            $context?->getParameters() ?? [],
        );

        $compoundResult = new Result();

        foreach ($rules ?? [] as $attribute => $attributeRules) {
            $tempRule = is_iterable($attributeRules) ? $attributeRules : [$attributeRules];
            $attributeRules = $this->normalizeRules($tempRule);

            if (is_int($attribute)) {
                $validatedData = $data->getData();
            } else {
                $validatedData = $data->getAttributeValue($attribute);
                $context = $context->withAttribute($attribute);
            }

            $this->validateInternal(
                $validatedData,
                $attributeRules,
                $context,
                $compoundResult,
            );
        }

        if ($data instanceof PostValidationHookInterface) {
            $data->processValidationResult($compoundResult);
        }

        return $compoundResult;
    }

    /**
     * @param iterable<Closure|Closure[]|RuleInterface|RuleInterface[]> $rules
     */
    private function validateInternal($value, iterable $rules, ValidationContext $context, Result $compoundResult): void
    {
        foreach ($rules as $rule) {
            if ($rule instanceof BeforeValidationInterface && $this->preValidate($value, $context, $rule)) {
                continue;
            }

            $ruleHandler = $this->ruleHandlerResolver->resolve($rule->getHandlerClassName());
            $ruleResult = $ruleHandler->validate($value, $rule, $context);
            if ($ruleResult->isValid()) {
                continue;
            }

            $context->setParameter($this->parameterPreviousRulesErrored, true);

            foreach ($ruleResult->getErrors() as $error) {
                $valuePath = $error->getValuePath();
                if ($context->getAttribute() !== null) {
                    $valuePath = [$context->getAttribute(), ...$valuePath];
                }
                $compoundResult->addError($error->getMessage(), $valuePath, $error->getParameters());
            }
        }
    }

    /**
     * @param array $rules
     *
     * @return iterable<RuleInterface>
     */
    private function normalizeRules(iterable $rules): iterable
    {
        foreach ($rules as $rule) {
            yield $this->normalizeRule($rule);
        }
    }

    private function normalizeRule($rule): RuleInterface
    {
        if (is_callable($rule)) {
            return new Callback($rule);
        }

        if (!$rule instanceof RuleInterface) {
            throw new InvalidArgumentException(
                sprintf(
                    'Rule should be either an instance of %s or a callable, %s given.',
                    RuleInterface::class,
                    get_debug_type($rule)
                )
            );
        }

        if ($rule instanceof SkipOnEmptyInterface && $rule->getSkipOnEmpty() === null) {
            $rule = $rule->skipOnEmpty($this->defaultSkipOnEmptyCallback);
        }

        return $rule;
    }

    #[Pure]
    private function normalizeDataSet($data): DataSetInterface
    {
        if ($data instanceof DataSetInterface) {
            return $data;
        }

        if (is_object($data)) {
            return new ObjectDataSet($data);
        }

        if (is_array($data)) {
            return new ArrayDataSet($data);
        }

        return new MixedDataSet($data);
    }
}
