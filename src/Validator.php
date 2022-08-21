<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Closure;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Yiisoft\Validator\DataSet\ArrayDataSet;
use Yiisoft\Validator\DataSet\ObjectDataSet;
use Yiisoft\Validator\DataSet\MixedDataSet;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Trait\PreValidateTrait;
use Yiisoft\Validator\SkipOnEmptyCallback\SkipNone;
use Yiisoft\Validator\SkipOnEmptyCallback\SkipOnEmpty;

use function gettype;
use function is_array;
use function is_callable;
use function is_int;
use function is_object;

/**
 * Validator validates {@link DataSetInterface} against rules set for data set attributes.
 */
final class Validator implements ValidatorInterface
{
    use PreValidateTrait;

    public function __construct(
        private RuleHandlerResolverInterface $ruleHandlerResolver,
        private ?bool $skipOnEmpty = null,
        /**
         * @var callable
         */
        private $skipOnEmptyCallback = null
    ) {
        if ($this->skipOnEmpty !== null) {
            $this->skipOnEmptyCallback = $this->skipOnEmpty === false ? new SkipNone() : new SkipOnEmpty();
        } elseif ($this->skipOnEmptyCallback !== null) {
            if (!is_callable($this->skipOnEmptyCallback)) {
                throw new InvalidArgumentException('$skipOnEmptyCallback must be a callable.');
            }

            $this->skipOnEmpty = true;
        }
    }

    public function getSkipOnEmpty(): ?bool
    {
        return $this->skipOnEmpty;
    }

    public function getSkipOnEmptyCallback(): ?callable
    {
        return $this->skipOnEmptyCallback;
    }

    /**
     * @param DataSetInterface|mixed|RulesProviderInterface $data
     * @param iterable<Closure|Closure[]|RuleInterface|RuleInterface[]>|null $rules
     */
    public function validate(mixed $data, ?iterable $rules = null): Result
    {
        $data = $this->normalizeDataSet($data);
        if ($rules === null && $data instanceof RulesProviderInterface) {
            $rules = $data->getRules();
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

        if ($this->skipOnEmpty !== null) {
            $rule = $rule->skipOnEmpty($this->skipOnEmpty);
        }

        if ($this->skipOnEmpty !== null) {
            $rule = $rule->skipOnEmptyCallback($this->skipOnEmptyCallback);
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
