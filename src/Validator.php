<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use InvalidArgumentException;
use ReflectionException;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\IdMessageReader;
use Yiisoft\Translator\IntlMessageFormatter;
use Yiisoft\Translator\SimpleMessageFormatter;
use Yiisoft\Translator\Translator;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Helper\DataSetNormalizer;
use Yiisoft\Validator\Helper\RulesNormalizer;
use Yiisoft\Validator\Helper\SkipOnEmptyNormalizer;

use function extension_loaded;
use function is_int;

/**
 * Validator validates {@link DataSetInterface} against rules set for data set attributes.
 *
 * @psalm-import-type RulesType from ValidatorInterface
 */
final class Validator implements ValidatorInterface
{
    public const DEFAULT_TRANSLATION_CATEGORY = 'yii-validator';
    private const PARAMETER_PREVIOUS_RULES_ERRORED = 'previousRulesErrored';

    private RuleHandlerResolverInterface $ruleHandlerResolver;
    private TranslatorInterface $translator;

    /**
     * @var callable
     */
    private $defaultSkipOnEmptyCriteria;

    public function __construct(
        ?RuleHandlerResolverInterface $ruleHandlerResolver = null,
        ?TranslatorInterface $translator = null,
        bool|callable|null $defaultSkipOnEmpty = null,
        private string $translationCategory = self::DEFAULT_TRANSLATION_CATEGORY,
    ) {
        $this->ruleHandlerResolver = $ruleHandlerResolver ?? new SimpleRuleHandlerContainer();
        $this->translator = $translator ?? $this->createDefaultTranslator();
        $this->defaultSkipOnEmptyCriteria = SkipOnEmptyNormalizer::normalize($defaultSkipOnEmpty);
    }

    /**
     * @param DataSetInterface|mixed|RulesProviderInterface $data
     *
     * @psalm-param RulesType $rules
     *
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function validate(
        mixed $data,
        callable|iterable|object|string|null $rules = null,
        ?ValidationContext $context = null
    ): Result {
        $dataSet = DataSetNormalizer::normalize($data);
        $rules = RulesNormalizer::normalize(
            $rules,
            $dataSet,
            $this->defaultSkipOnEmptyCriteria
        );

        $context ??= new ValidationContext();
        $context
            ->setValidatorAndRawDataOnce($this, $data)
            ->setDataSet($dataSet);

        $results = [];
        foreach ($rules as $attribute => $attributeRules) {
            $result = new Result();

            if (is_int($attribute)) {
                /** @psalm-suppress MixedAssignment */
                $validatedData = $dataSet->getData();
                $context->setAttribute(null);
            } else {
                /** @psalm-suppress MixedAssignment */
                $validatedData = $dataSet->getAttributeValue($attribute);
                $context->setAttribute($attribute);
            }

            $tempResult = $this->validateInternal($validatedData, $attributeRules, $context);

            foreach ($tempResult->getErrors() as $error) {
                $result->addError($error->getMessage(), $error->getParameters(), $error->getValuePath());
            }

            $results[] = $result;
        }

        $compoundResult = new Result();

        foreach ($results as $result) {
            foreach ($result->getErrors() as $error) {
                $compoundResult->addError(
                    $this->translator->translate(
                        $error->getMessage(),
                        $error->getParameters(),
                        $this->translationCategory
                    ),
                    $error->getParameters(),
                    $error->getValuePath()
                );
            }
        }

        if ($dataSet instanceof PostValidationHookInterface) {
            $dataSet->processValidationResult($compoundResult);
        }

        return $compoundResult;
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

            $context->setParameter(self::PARAMETER_PREVIOUS_RULES_ERRORED, true);

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

    private function preValidate(mixed $value, ValidationContext $context, RuleInterface $rule): bool
    {
        if (
            $rule instanceof SkipOnEmptyInterface &&
            (SkipOnEmptyNormalizer::normalize($rule->getSkipOnEmpty()))($value, $context->isAttributeMissing())
        ) {
            return true;
        }

        if (
            $rule instanceof SkipOnErrorInterface
            && $rule->shouldSkipOnError()
            && $context->getParameter(self::PARAMETER_PREVIOUS_RULES_ERRORED) === true
        ) {
            return true;
        }

        if ($rule instanceof WhenInterface) {
            $when = $rule->getWhen();
            return $when !== null && !$when($value, $context);
        }

        return false;
    }

    private function createDefaultTranslator(): Translator
    {
        $categorySource = new CategorySource(
            $this->translationCategory,
            new IdMessageReader(),
            extension_loaded('intl') ? new IntlMessageFormatter() : new SimpleMessageFormatter(),
        );
        $translator = new Translator();
        $translator->addCategorySources($categorySource);

        return $translator;
    }
}
