<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use RuntimeException;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;

/**
 * Validation context that might be taken into account when performing validation.
 * The context is passed to both direct calls of {@see ValidatorInterface::validate()} and when {@see Nested}
 * or {@see Each} is used.
 *
 * @psalm-import-type RulesType from ValidatorInterface
 */
final class ValidationContext
{
    /**
     * @var ValidatorInterface|null A validator instance. Null means context data was not set yet
     * with {@see setContextDataOnce()}.
     */
    private ?ValidatorInterface $validator = null;

    /**
     * @var mixed The raw validated data. Null means context data was not set yet with {@see setContextDataOnce()}.
     */
    private mixed $rawData = null;

    /**
     * @var DataSetInterface|null Data set the attribute belongs to.
     * Null if data set was not set yet with {@see setDataSet()}.
     */
    private ?DataSetInterface $dataSet = null;

    /**
     * @var string|null Validated data set attribute name. Null if a single value is validated.
     */
    private ?string $attribute = null;

    /**
     * @var AttributeTranslatorInterface|null Default attribute translator to use if attribute translator is not set.
     */
    private ?AttributeTranslatorInterface $defaultAttributeTranslator = null;

    /**
     * @param array $parameters Arbitrary parameters.
     * @param AttributeTranslatorInterface|null $attributeTranslator Optional attribute translator instance to use.
     * If null is provided, or it's not specified, default passed through {@see setContextDataOnce()} is used.
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
     * @return $this
     */
    public function setContextDataOnce(
        ValidatorInterface $validator,
        AttributeTranslatorInterface $attributeTranslator,
        mixed $rawData
    ): self {
        if ($this->validator !== null) {
            return $this;
        }

        $this->validator = $validator;
        $this->defaultAttributeTranslator = $attributeTranslator;
        $this->rawData = $rawData;

        return $this;
    }

    /**
     * Set attribute translator to use.
     *
     * @param AttributeTranslatorInterface|null $attributeTranslator Attribute translator to use. If null,
     * translator passed in {@see setContextData()} will be used.
     * @return $this
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
     * @psalm-param RulesType $rules
     */
    public function validate(mixed $data, callable|iterable|object|string|null $rules = null): Result
    {
        $this->checkValidatorAndRawData();

        $currentDataSet = $this->dataSet;
        $currentAttribute = $this->attribute;

        $result = $this->validator->validate($data, $rules, $this);

        $this->dataSet = $currentDataSet;
        $this->attribute = $currentAttribute;

        return $result;
    }

    /**
     * @return mixed The raw validated data.
     */
    public function getRawData(): mixed
    {
        $this->checkValidatorAndRawData();
        return $this->rawData;
    }

    /**
     * @return DataSetInterface Data set the attribute belongs to.
     */
    public function getDataSet(): DataSetInterface
    {
        if ($this->dataSet === null) {
            throw new RuntimeException('Data set in validation context is not set.');
        }

        return $this->dataSet;
    }

    /**
     * @param DataSetInterface $dataSet Data set the attribute belongs to.
     */
    public function setDataSet(DataSetInterface $dataSet): self
    {
        $this->dataSet = $dataSet;
        return $this;
    }

    /**
     * @return string|null Validated attribute name. Null if a single value is validated.
     */
    public function getAttribute(): ?string
    {
        return $this->attribute;
    }

    /**
     * Get translated attribute name.
     *
     * @return string|null Translated attribute name.
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
     * Specify name of the attribute validated.
     *
     * @param string|null $attribute Validated attribute name. Null if a single value is validated.
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
     * @return $this
     */
    public function setParameter(string $name, mixed $value): self
    {
        $this->parameters[$name] = $value;
        return $this;
    }

    /**
     * @return bool If attribute is missing.
     */
    public function isAttributeMissing(): bool
    {
        return $this->attribute !== null && $this->dataSet !== null && !$this->dataSet->hasAttribute($this->attribute);
    }

    /**
     * @psalm-assert ValidatorInterface $this->validator
     */
    private function checkValidatorAndRawData(): void
    {
        if ($this->validator === null) {
            throw new RuntimeException('Validator and raw data in validation context is not set.');
        }
    }
}
