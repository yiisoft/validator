<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\IdMessageReader;
use Yiisoft\Translator\IntlMessageFormatter;
use Yiisoft\Translator\SimpleMessageFormatter;
use Yiisoft\Translator\Translator;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\AttributeTranslator\TranslatorAttributeTranslator;
use Yiisoft\Validator\Helper\DataSetNormalizer;
use Yiisoft\Validator\Helper\RulesNormalizer;
use Yiisoft\Validator\Helper\SkipOnEmptyNormalizer;
use Yiisoft\Validator\RuleHandlerResolver\SimpleRuleHandlerContainer;

use function extension_loaded;
use function is_int;
use function is_string;

/**
 * The only built-in implementation of {@see ValidatorInterface}, the main class / entry point processing all the data
 * and rules with validation context together and performing the actual validation.
 */
final class Validator implements ValidatorInterface
{
    /**
     * A name for {@see CategorySource} used with translator ({@see TranslatorInterface}) by default.
     */
    public const DEFAULT_TRANSLATION_CATEGORY = 'yii-validator';

    /**
     * @var RuleHandlerResolverInterface A container to resolve rule handler names to corresponding instances.
     */
    private RuleHandlerResolverInterface $ruleHandlerResolver;
    /**
     * @var TranslatorInterface A translator instance used for translations of error messages. If it was not set
     * explicitly in the constructor, a default one created automatically in {@see createDefaultTranslator()}.
     */
    private TranslatorInterface $translator;
    /**
     * @var callable A default "skip on empty" condition ({@see SkipOnEmptyInterface}), already normalized. Used to
     * optimize setting the same value in all the rules.
     */
    private $defaultSkipOnEmptyCondition;
    /**
     * @var AttributeTranslatorInterface A default translator used for translation of rule ({@see RuleInterface})
     * attributes. Used to optimize setting the same value in all the rules.
     */
    private AttributeTranslatorInterface $defaultAttributeTranslator;

    /**
     * @param RuleHandlerResolverInterface|null $ruleHandlerResolver Optional container to resolve rule handler names to
     * corresponding instances. If not provided, {@see SimpleRuleContainer} used as a default one.
     * @param TranslatorInterface|null $translator Optional translator instance used for translations of error messages.
     * If not provided, a default one is created via {@see createDefaultTranslator()}.
     * @param bool|callable|null $defaultSkipOnEmpty Raw non-normalized "skip on empty" value (see
     * {@see SkipOnEmptyInterface::getSkipOnEmpty()}).
     * @param string $translationCategory A name for {@see CategorySource} used during creation
     * ({@see createDefaultTranslator()}) of default translator ({@see TranslatorInterface}) in case `$translator`
     * argument was not specified explicitly. If not provided, a {@see DEFAULT_TRANSLATION_CATEGORY} will be used.
     * @param AttributeTranslatorInterface|null $defaultAttributeTranslator A default translator used for translation of
     * rule ({@see RuleInterface}) attributes. If not provided, a {@see TranslatorAttributeTranslator} will be used.
     */
    public function __construct(
        ?RuleHandlerResolverInterface $ruleHandlerResolver = null,
        ?TranslatorInterface $translator = null,
        bool|callable|null $defaultSkipOnEmpty = null,
        private string $translationCategory = self::DEFAULT_TRANSLATION_CATEGORY,
        ?AttributeTranslatorInterface $defaultAttributeTranslator = null,
    ) {
        $this->ruleHandlerResolver = $ruleHandlerResolver ?? new SimpleRuleHandlerContainer();
        $this->translator = $translator ?? $this->createDefaultTranslator();
        $this->defaultSkipOnEmptyCondition = SkipOnEmptyNormalizer::normalize($defaultSkipOnEmpty);
        $this->defaultAttributeTranslator = $defaultAttributeTranslator
            ?? new TranslatorAttributeTranslator($this->translator);
    }

    /**
     * An immutable setter to change default "skip on empty" condition.
     *
     * @param bool|callable|null $value A new raw non-normalized "skip on empty" value (see
     * {@see SkipOnEmptyInterface::getSkipOnEmpty()}).
     *
     * @return $this The new instance with a changed value.
     *
     * @see $defaultSkipOnEmptyCondition
     */
    public function withDefaultSkipOnEmptyCondition(bool|callable|null $value): static
    {
        $new = clone $this;
        $new->defaultSkipOnEmptyCondition = SkipOnEmptyNormalizer::normalize($value);
        return $new;
    }

    public function validate(
        mixed $data,
        callable|iterable|object|string|null $rules = null,
        ?ValidationContext $context = null
    ): Result {
        $dataSet = DataSetNormalizer::normalize($data);
        /** @psalm-suppress MixedAssignment */
        $originalData = $dataSet instanceof DataWrapperInterface ? $dataSet->getSource() : $data;

        $rules = RulesNormalizer::normalize(
            $rules,
            $dataSet,
            $this->defaultSkipOnEmptyCondition
        );

        $defaultAttributeTranslator =
            ($dataSet instanceof AttributeTranslatorProviderInterface ? $dataSet->getAttributeTranslator() : null)
            ?? $this->defaultAttributeTranslator;

        $context ??= new ValidationContext();
        $context
            ->setContextDataOnce($this, $defaultAttributeTranslator, $data, $dataSet)
            ->setDataSet($dataSet);

        $result = new Result();
        foreach ($rules as $attribute => $attributeRules) {
            if (is_int($attribute)) {
                /** @psalm-suppress MixedAssignment */
                $validatedData = $originalData;
                $context->setParameter(ValidationContext::PARAMETER_VALUE_AS_ARRAY, $dataSet->getData());
                $context->setAttribute(null);
            } else {
                /** @psalm-suppress MixedAssignment */
                $validatedData = $dataSet->getAttributeValue($attribute);
                $context->setParameter(ValidationContext::PARAMETER_VALUE_AS_ARRAY, null);
                $context->setAttribute($attribute);
            }

            $tempResult = $this->validateInternal($validatedData, $attributeRules, $context);

            foreach ($tempResult->getErrors() as $error) {
                $result->addError(
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

        if ($originalData instanceof PostValidationHookInterface) {
            $originalData->processValidationResult($result);
        }

        return $result;
    }

    /**
     * Validates input of any type according to normalized rules and validation context. Aggregates errors from all the
     * rules to a one unified result.
     *
     * @param mixed $value The validated value of any type.
     * @param iterable $rules Normalized rules ({@see RuleInterface} that can be iterated).
     * @psalm-param iterable<RuleInterface> $rules
     *
     * @param ValidationContext $context Validation context.
     *
     * @return Result The result of validation.
     */
    private function validateInternal(mixed $value, iterable $rules, ValidationContext $context): Result
    {
        $compoundResult = new Result();
        foreach ($rules as $rule) {
            if ($this->shouldSkipRule($rule, $value, $context)) {
                continue;
            }

            $ruleHandler = $rule->getHandler();
            if (is_string($ruleHandler)) {
                $ruleHandler = $this->ruleHandlerResolver->resolve($ruleHandler);
            }

            $ruleResult = $ruleHandler->validate($value, $rule, $context);
            if ($ruleResult->isValid()) {
                continue;
            }

            $context->setParameter(ValidationContext::PARAMETER_PREVIOUS_RULES_ERRORED, true);

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
     * Acts like a pre-validation phase allowing to skip validation for specific rule within a set if any of these
     * conditions are met:
     *
     * - The value is empty / not passed ({@see SkipOnEmptyInterface}).
     * - The previous rule in the set caused error and the current one was configured for skipping in case of such error
     * occured ({@see SkipOnErrorInterface}).
     * - "when" callable returned `false` {@see WhenInterface}.
     *
     * @param RuleInterface $rule A rule instance.
     * @param mixed $value The validated value of any type.
     * @param ValidationContext $context Validation context.
     *
     * @return bool Whether to skip validation for this rule - `true` means skip and `false` to not skip.
     */
    private function shouldSkipRule(RuleInterface $rule, mixed $value, ValidationContext $context): bool
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
            && $context->getParameter(ValidationContext::PARAMETER_PREVIOUS_RULES_ERRORED) === true
        ) {
            return true;
        }

        if ($rule instanceof WhenInterface) {
            $when = $rule->getWhen();
            return $when !== null && !$when($value, $context);
        }

        return false;
    }

    /**
     * Creates default translator to use if {@see $translator} was not set explicitly in the constructor. Depending on
     * "intl" extension availability, either {@see IntlMessageFormatter} or {@see SimpleMessageFormatter} is used as
     * formatter.
     *
     * @return Translator Translator instance used for translations of error messages.
     */
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
