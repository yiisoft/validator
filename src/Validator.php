<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Yiisoft\Validator\DataSet\ArrayDataSet;
use Yiisoft\Validator\DataSet\ScalarDataSet;
use Yiisoft\Validator\Rule\Callback;

use Yiisoft\Validator\Rule\Trait\EmptyCheckTrait;

use function is_array;
use function is_object;

/**
 * Validator validates {@link DataSetInterface} against rules set for data set attributes.
 */
final class Validator implements ValidatorInterface
{
    use EmptyCheckTrait;

    public const PARAMETER_PREVIOUS_RULES_ERRORED = 'previousRulesErrored';

    public function __construct(private RuleHandlerResolverInterface $ruleHandlerResolver)
    {
    }

    /**
     * @param DataSetInterface|mixed|RulesProviderInterface $data
     * @param iterable<RuleInterface|RuleInterface[]> $rules
     */
    public function validate($data, iterable $rules = []): Result
    {
        $data = $this->normalizeDataSet($data);
        if ($data instanceof RulesProviderInterface) {
            $rules = $data->getRules();
        }

        $context = new ValidationContext($this, $data);
        $compoundResult = new Result();

        $results = [];

        foreach ($rules as $attribute => $attributeRules) {
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
    private function normalizeDataSet($data): DataSetInterface
    {
        if ($data instanceof DataSetInterface) {
            return $data;
        }

        if (is_object($data) || is_array($data)) {
            return new ArrayDataSet((array)$data);
        }

        return new ScalarDataSet($data);
    }

    /**
     * @param $value
     * @param iterable<RuleInterface> $rules
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
            if ($rule instanceof PreValidatableRuleInterface) {
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

            $context->setParameter(self::PARAMETER_PREVIOUS_RULES_ERRORED, true);

            foreach ($ruleResult->getErrors() as $error) {
                $compoundResult->addError($error->getMessage(), $error->getValuePath());
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

    private function preValidate(
        $value,
        ValidationContext $context,
        PreValidatableRuleInterface $rule
    ): bool {
        if ($rule->isSkipOnEmpty() && $this->isEmpty($value)) {
            return true;
        }

        if ($rule->isSkipOnError() && $context->getParameter(self::PARAMETER_PREVIOUS_RULES_ERRORED) === true) {
            return true;
        }

        return (bool) (is_callable($rule->getWhen()) && !($rule->getWhen())($value, $context))



         ;
    }
}
