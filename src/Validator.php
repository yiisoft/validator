<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Closure;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Yiisoft\Validator\DataSet\ArrayDataSet;
use Yiisoft\Validator\DataSet\MixedDataSet;
use Yiisoft\Validator\DataSet\ObjectDataSet;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Trait\PreValidateTrait;

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
    ) {
        $this->defaultSkipOnEmptyCallback = SkipOnEmptyNormalizer::normalize($defaultSkipOnEmpty);
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
        iterable|RulesProviderInterface|null $rules = null,
        ?ValidationContext $context = null,
    ): Result {
        $data = $this->normalizeDataSet($data);

        $compoundResult = new Result();
        $context = new ValidationContext($context?->getValidator() ?? $this, $context?->getDataSet() ?? $data, $context?->getAttribute() ?? null, $context?->getParameters() ?? []);
        $results = [];

//        var_dump($rules);
//        var_dump('qqqqqq');
//        exit();
        foreach ($rules ?? [] as $attribute => $attributeRules) {
            $result = new Result();

            $tempRule = is_iterable($attributeRules) ? $attributeRules : [$attributeRules];
            $attributeRules = $this->normalizeRules($tempRule);

            if (is_int($attribute)) {
                $validatedData = $data->getData();
                $validatedContext = $context;
            } else {
                $validatedData = $data->getAttributeValue($attribute);
                $validatedContext = $context->withAttribute($attribute);
            }

            $tempResult = $this->validateInternal(
                $validatedData,
                $attributeRules,
                $validatedContext
            );

            $result = $this->addErrors($result, $tempResult->getErrors());
            $results[] = $result;
        }

        foreach ($results as $result) {
            $compoundResult = $this->addErrors($compoundResult, $result->getErrors());
        }

        if ($data instanceof PostValidationHookInterface) {
            $data->processValidationResult($compoundResult);
        }

        return $compoundResult;
    }

    /**
     * @param $value
     * @param iterable<Closure|Closure[]|RuleInterface|RuleInterface[]> $rules
     * @param ValidationContext $context
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @return Result
     */
    private function validateInternal($value, iterable $rules, ValidationContext $context): Result
    {
        $compoundResult = new Result();
        foreach ($rules as $rule) {
            if ($rule instanceof BeforeValidationInterface) {
                $preValidateResult = $this->preValidate($value, $context, $rule);
                if ($preValidateResult) {
                    continue;
                }
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
        return $compoundResult;
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

    /**
     * @param Result $result
     * @param Error[] $errors
     *
     * @return Result
     */
    private function addErrors(Result $result, array $errors): Result
    {
        foreach ($errors as $error) {
            $result->addError($error->getMessage(), $error->getValuePath(), $error->getParameters());
        }
        return $result;
    }
}
