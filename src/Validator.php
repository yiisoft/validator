<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Yiisoft\Validator\DataSet\ArrayDataSet;
use Yiisoft\Validator\DataSet\ScalarDataSet;
use Yiisoft\Validator\Rule\Callback\Callback;
use function is_array;
use function is_object;

/**
 * Validator validates {@link DataSetInterface} against rules set for data set attributes.
 */
final class Validator implements ValidatorInterface
{
    public const PARAMETER_PREVIOUS_RULES_ERRORED = 'previousRulesErrored';

    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param DataSetInterface|mixed|RulesProviderInterface $data
     * @param iterable<RuleInterface>|iterable<RuleInterface[]> $rules
     */
    public function validate($data, iterable $rules = []): Result
    {
        $data = $this->normalizeDataSet($data);
        if ($data instanceof RulesProviderInterface) {
            $rules = $data->getRules();
        }

        $context = new ValidationContext($data);
        $compoundResult = new Result();

        $results = [];

        foreach ($rules as $attribute => $attributeRules) {
            $attributeName = is_string($attribute) ? $attribute : null;
            $result = new Result($attributeName);

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

            foreach ($tempResult->getErrors() as $error) {
                $result->merge($error);
            }
            $results[] = $result;
        }

        foreach ($results as $result) {
            foreach ($result->getErrors() as $error) {
                $compoundResult->merge($error);
            }
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
            $ruleValidator = $this->container->get($rule->getValidatorClassName());
            $ruleResult = $ruleValidator->validate($value, $rule, $this, $context);
            if ($ruleResult->isValid()) {
                continue;
            }

            $context->setParameter(self::PARAMETER_PREVIOUS_RULES_ERRORED, true);

            foreach ($ruleResult->getErrors() as $error) {
                $compoundResult->merge($error);
            }
        }
        return $compoundResult;
    }

    /**
     * @param array $rules
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
            throw new InvalidArgumentException(sprintf(
                'Rule should be either an instance of %s or a callable, %s given.',
                RuleInterface::class,
                gettype($rule)
            ));
        }

        return $rule;
    }
}
