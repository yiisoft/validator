<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use RuntimeException;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\Rule\StopOnError;

/**
 * Validation context that might be taken into account when performing validation.
 *
 * @psalm-import-type RawRules from ValidatorInterface
 */
final class ValidationContext
{
    /**
     * A name of parameter storing validated value as array. For rules working with arrays it helps to prevent extra
     * conversion of a validated value to array. The parameter's value type is either `array` or `null`. `null` means
     * the original value must be used.
     */
    public const PARAMETER_VALUE_AS_ARRAY = 'yii-validator-value-as-array';

    /**
     * A name of parameter indicating that previous rule in the set caused validation error. Used to allow skipping of
     * the current rule:
     *
     * - in {@see Validator} for rules implementing {@see SkipOnErrorInterface}.
     * - for {@see StopOnError} rule (no additional configuration is needed).
     */
    public const PARAMETER_PREVIOUS_RULES_ERRORED = 'yii-validator-previous-rules-errored';

    /**
     * @var ValidatorInterface|null A validator instance. `null` means context data was not set
     * with {@see setContextDataOnce()} yet.
     */
    private ?ValidatorInterface $validator = null;

    /**
     * @var mixed The raw validated data. `null` means context data was not set with {@see setContextDataOnce()} yet.
     */
    private mixed $rawData = null;

    /**
     * @var DataSetInterface|null Global data set. `null` if data set was not set with {@see setContextDataOnce()} yet.
     */
    private ?DataSetInterface $globalDataSet = null;

    /**
     * @var DataSetInterface|null Current scope's data set the attribute belongs to. `null` if data set was not set
     * with {@see setDataSet()} yet.
     */
    private ?DataSetInterface $dataSet = null;

    /**
     * @var string|null Validated data set's attribute name. `null` if a single value is validated.
     */
    private ?string $attribute = null;

    /**
     * @var AttributeTranslatorInterface|null Default attribute translator to use if attribute translator is not set.
     */
    private ?AttributeTranslatorInterface $defaultAttributeTranslator = null;

    /**
     * @var bool Whether {@see $dataSet} is missing.
     */
    private bool $isDataSetMissing = false;

    /**
     * @param array $parameters Arbitrary parameters.
     * @param AttributeTranslatorInterface|null $attributeTranslator Optional attribute translator instance to use.
     * If `null` is provided, or it's not specified, a default translator passed through
     * {@see setContextDataOnce()} is used.
     */
    public function __construct(
        private array $parameters = [],
        private ?AttributeTranslatorInterface $attributeTranslator = null,
    ) {
    }

    /**
     * Set context data if it is not set yet.
     *
     * @param ValidatorInterface $validator A validator instance.
     * @param AttributeTranslatorInterface $attributeTranslator Attribute translator to use by default. If translator
     * is specified via {@see setAttributeTranslator()}, it will be used instead.
     * @param mixed $rawData The raw validated data.
     * @param DataSetInterface $dataSet Global data set ({@see $globalDataSet}).
     *
     * @internal
     *
     * @return $this The same instance of validation context.
     */
    public function setContextDataOnce(
        ValidatorInterface $validator,
        AttributeTranslatorInterface $attributeTranslator,
        mixed $rawData,
        DataSetInterface $dataSet,
    ): self {
        if ($this->validator !== null) {
            return $this;
        }

        $this->validator = $validator;
        $this->defaultAttributeTranslator = $attributeTranslator;
        $this->rawData = $rawData;
        $this->globalDataSet = $dataSet;

        return $this;
    }

    /**
     * Set attribute translator to use.
     *
     * @param AttributeTranslatorInterface|null $attributeTranslator Attribute translator to use. If `null`,
     * translator passed in {@see setContextData()} will be used.
     *
     * @return $this The same instance of validation context.
     */
    public function setAttributeTranslator(?AttributeTranslatorInterface $attributeTranslator): self
    {
        $this->attributeTranslator = $attributeTranslator;
        return $this;
    }

    /**
     * Validate data in current context.
     *
     * @param mixed $data Data set to validate. If {@see RulesProviderInterface} instance provided and rules are
     * not specified explicitly, they are read from the {@see RulesProviderInterface::getRules()}.
     * @param callable|iterable|object|string|null $rules Rules to apply. If specified, rules are not read from data set
     * even if it is an instance of {@see RulesProviderInterface}.
     *
     * @psalm-param RawRules|null $rules
     *
     * @throws RuntimeException If validator is not set in validation context.
     *
     * @return Result Validation result.
     */
    public function validate(mixed $data, callable|iterable|object|string|null $rules = null): Result
    {
        $this->requireValidator();

        $currentDataSet = $this->dataSet;
        $currentAttribute = $this->attribute;
        $isCurrentDataSetMissing = $this->isDataSetMissing;

        // The lack of an attribute means that in the context of further validation there is no data set at all.
        $this->isDataSetMissing = $this->isAttributeMissing();
        $result = $this->validator->validate($data, $rules, $this);

        $this->dataSet = $currentDataSet;
        $this->attribute = $currentAttribute;
        $this->isDataSetMissing = $isCurrentDataSetMissing;

        return $result;
    }

    /**
     * Get the raw validated data.
     *
     * @throws RuntimeException If validator is not set in validation context.
     *
     * @return mixed The raw validated data.
     */
    public function getRawData(): mixed
    {
        $this->requireValidator();
        return $this->rawData;
    }

    /**
     * Get the global data set.
     *
     * @return DataSetInterface Data set instance.
     *
     * @see $globalDataSet
     */
    public function getGlobalDataSet(): DataSetInterface
    {
        $this->requireValidator();
        return $this->globalDataSet;
    }

    /**
     * Get the current scope's data set the attribute belongs to.
     *
     * @return DataSetInterface Data set instance.
     *
     * @see $dataSet
     */
    public function getDataSet(): DataSetInterface
    {
        if ($this->dataSet === null) {
            throw new RuntimeException('Data set in validation context is not set.');
        }

        return $this->dataSet;
    }

    /**
     * Set the current scope's data set the attribute belongs to.
     *
     * @param DataSetInterface $dataSet Data set instance.
     *
     * @return $this The same instance of validation context.
     *
     * @internal
     *
     * @see $dataSet
     */
    public function setDataSet(DataSetInterface $dataSet): self
    {
        $this->dataSet = $dataSet;
        return $this;
    }

    /**
     * Get validated data set's attribute name.
     *
     * @return string|null Validated data set's attribute name. `null` if a single value is validated.
     */
    public function getAttribute(): ?string
    {
        return $this->attribute;
    }

    /**
     * Get translated attribute name.
     *
     * @return string|null Translated attribute name. `null` if a single value is validated and there is nothing
     * to translate.
     */
    public function getTranslatedAttribute(): ?string
    {
        if ($this->attribute === null) {
            return null;
        }

        if ($this->attributeTranslator !== null) {
            return $this->attributeTranslator->translate($this->attribute);
        }

        if ($this->defaultAttributeTranslator !== null) {
            return $this->defaultAttributeTranslator->translate($this->attribute);
        }

        return $this->attribute;
    }

    /**
     * Set the name of the attribute validated.
     *
     * @param string|null $attribute Validated attribute name. Null if a single value is validated.
     *
     * @return $this The same instance of validation context.
     *
     * @internal
     */
    public function setAttribute(?string $attribute): self
    {
        $this->attribute = $attribute;
        return $this;
    }

    /**
     * Get named parameter.
     *
     * @param string $name Parameter name.
     * @param mixed $default Default value to return in case parameter with a given name does not exist.
     *
     * @return mixed Parameter value.
     *
     * @see ArrayHelper::getValue()
     */
    public function getParameter(string $name, mixed $default = null): mixed
    {
        return ArrayHelper::getValue($this->parameters, $name, $default);
    }

    /**
     * Set parameter value.
     *
     * @param string $name Parameter name.
     * @param mixed $value Parameter value.
     *
     * @return $this The same instance of validation context.
     */
    public function setParameter(string $name, mixed $value): self
    {
        $this->parameters[$name] = $value;
        return $this;
    }

    /**
     * Check whether {@see $attribute} is missing in a {@see $dataSet}.
     *
     * @return bool Whether {@see $attribute} is missing in a {@see $dataSet}.
     */
    public function isAttributeMissing(): bool
    {
        return $this->isDataSetMissing
            || ($this->attribute !== null && !$this->getDataSet()->hasAttribute($this->attribute));
    }

    /**
     * Ensure that validator is set in validation context.
     *
     * @psalm-assert ValidatorInterface $this->validator
     * @psalm-assert DataSetInterface $this->globalDataSet
     *
     * @throws RuntimeException If validator is not set in validation context.
     */
    private function requireValidator(): void
    {
        if ($this->validator === null) {
            throw new RuntimeException('Validator is not set in validation context.');
        }
    }
}
