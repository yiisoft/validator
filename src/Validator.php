<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use ReflectionException;
use ReflectionProperty;
use Traversable;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\DataSet\ArrayDataSet;
use Yiisoft\Validator\DataSet\ObjectDataSet;
use Yiisoft\Validator\DataSet\SingleValueDataSet;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Trait\PreValidateTrait;
use Yiisoft\Validator\RulesProvider\AttributesRulesProvider;

use function is_array;
use function is_callable;
use function is_int;
use function is_object;
use function is_string;

/**
 * Validator validates {@link DataSetInterface} against rules set for data set attributes.
 *
 * @psalm-import-type RulesType from ValidatorInterface
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
        private TranslatorInterface $translator,
        /**
         * @var int What visibility levels to use when reading rules from the class specified in `$rules` argument in
         * {@see validate()} method.
         */
        private int $rulesPropertyVisibility = ReflectionProperty::IS_PRIVATE
        | ReflectionProperty::IS_PROTECTED
        | ReflectionProperty::IS_PUBLIC,
        bool|callable|null $defaultSkipOnEmpty = null,
    ) {
        $this->defaultSkipOnEmptyCallback = SkipOnEmptyNormalizer::normalize($defaultSkipOnEmpty);
    }

    /**
     * @param DataSetInterface|mixed|RulesProviderInterface $data
     * @psalm-param RulesType $rules
     * @throws ReflectionException
     */
    public function validate(mixed $data, iterable|object|string|null $rules = null): Result
    {
        $data = $this->normalizeDataSet($data);
        if ($rules === null && $data instanceof RulesProviderInterface) {
            $rules = $data->getRules();
        } elseif ($rules instanceof RulesProviderInterface) {
            $rules = $rules->getRules();
        } elseif ($rules instanceof RuleInterface) {
            $rules = [$rules];
        } elseif (is_string($rules) || (is_object($rules) && !$rules instanceof Traversable)) {
            $rules = (new AttributesRulesProvider($rules, $this->rulesPropertyVisibility))->getRules();
        }

        $compoundResult = new Result();
        $context = new ValidationContext($this, $data);
        $results = [];

        /**
         * @var mixed $attribute
         * @var mixed $attributeRules
         */
        foreach ($rules ?? [] as $attribute => $attributeRules) {
            $result = new Result();

            if (!is_iterable($attributeRules)) {
                $attributeRules = [$attributeRules];
            }

            $attributeRules = $this->normalizeRules($attributeRules);

            if (is_int($attribute)) {
                /** @psalm-suppress MixedAssignment */
                $validatedData = $data->getData();
            } elseif (is_string($attribute)) {
                /** @psalm-suppress MixedAssignment */
                $validatedData = $data->getAttributeValue($attribute);
                $context = $context->withAttribute($attribute);
            } else {
                $message = sprintf(
                    'An attribute can only have an integer or a string type. %s given.',
                    get_debug_type($attribute),
                );

                throw new InvalidArgumentException($message);
            }

            $tempResult = $this->validateInternal($validatedData, $attributeRules, $context);

            foreach ($tempResult->getErrors() as $error) {
                $result->addError($error->getMessage(), $error->getParameters(), $error->getValuePath());
            }

            $results[] = $result;
        }

        foreach ($results as $result) {
            foreach ($result->getErrors() as $error) {
                $compoundResult->addError(
                    $this->translator->translate($error->getMessage(), $error->getParameters()),
                    $error->getParameters(),
                    $error->getValuePath()
                );
            }
        }

        if ($data instanceof PostValidationHookInterface) {
            $data->processValidationResult($compoundResult);
        }

        return $compoundResult;
    }

    #[Pure]
    private function normalizeDataSet(mixed $data): DataSetInterface
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

        return new SingleValueDataSet($data);
    }

    /**
     * @param iterable<RuleInterface> $rules
     */
    private function validateInternal(mixed $value, iterable $rules, ValidationContext $context): Result
    {
        $compoundResult = new Result();
        foreach ($rules as $rule) {
            if ($this->preValidate($value, $context, $rule)) {
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
                $compoundResult->addError($error->getMessage(), $error->getParameters(), $valuePath);
            }
        }
        return $compoundResult;
    }

    /**
     * @return iterable<RuleInterface>
     */
    private function normalizeRules(iterable $rules): iterable
    {
        foreach ($rules as $rule) {
            yield $this->normalizeRule($rule);
        }
    }

    private function normalizeRule(mixed $rule): RuleInterface
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
}
