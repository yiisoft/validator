<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Yiisoft\Validator\DataSet\AttributeDataSet;
use Yiisoft\Validator\DataSet\ArrayDataSet;
use Yiisoft\Validator\DataSet\AttributeDataSet;
use Yiisoft\Validator\DataSet\ScalarDataSet;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Trait\PreValidateTrait;

use function is_array;
use function is_object;

/**
 * Validator validates {@link DataSetInterface} against rules set for data set attributes.
 */
final class Validator implements ValidatorInterface
{
    use PreValidateTrait;

    public function __construct(private RuleHandlerResolverInterface $ruleHandlerResolver)
    {
    }

    /**
     * @param DataSetInterface|mixed|RulesProviderInterface $data
     * @param iterable<\Closure|\Closure[]|RuleInterface|RuleInterface[]>|null $rules
     */
    public function validate(mixed $data, ?iterable $rules = null): Result
    {
        $data = $this->normalizeDataSet($data, $rules !== null);
        if ($rules === null && $data instanceof RulesProviderInterface) {
            $rules = (array) $data->getRules();
            $rulesByAttribute = (array) ((new AttributeDataSet($data))->getRules());
            $rules = array_merge($rulesByAttribute, $rules);
        }

        $compoundResult = new Result();
        $context = new ValidationContext($this, $data);
        $results = [];

        foreach ($rules ?? [] as $attribute => $attributeRules) {
            $result = new Result();

            $tempRule = is_array($attributeRules) ? $attributeRules : [$attributeRules];
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

    #[Pure]
    private function normalizeDataSet($data, bool $hasRules): DataSetInterface
    {
        if ($data instanceof DataSetInterface) {
            return $hasRules ? new AttributeDataSet($data) : $data;
        }

        if (is_object($data) || is_array($data)) {
            return new ArrayDataSet((array)$data);
        }

        return new ScalarDataSet($data);
    }

    /**
     * @param $value
     * @param iterable<\Closure|\Closure[]|RuleInterface|RuleInterface[]> $rules
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
                    $valuePath = [$context->getAttribute()] + $valuePath;
                }
                $compoundResult->addError($error->getMessage(), $valuePath);
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
                    gettype($rule)
                )
            );
        }

        return $rule;
    }

    private function addErrors(Result $result, array $errors): Result
    {
        foreach ($errors as $error) {
            $result->addError($error->getMessage(), $error->getValuePath());
        }
        return $result;
    }
}
