<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use RuntimeException;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\Helper\DataSetNormalizer;

/**
 * Validation context that might be taken into account when performing validation.
 *
 * @psalm-import-type RulesType from ValidatorInterface
 */
final class ValidationContext
{
    /**
     * @var DataSetInterface|null Data set the attribute belongs to. Null if data set not set.
     */
    private ?DataSetInterface $dataSet = null;

    /**
     * @param mixed $rawData The raw validated data.
     * @param string|null $attribute Validated attribute name. Null if a single value is validated.
     * @param array $parameters Arbitrary parameters.
     */
    public function __construct(
        private ValidatorInterface $validator,
        private mixed $rawData,
        private ?string $attribute = null,
        private array $parameters = []
    ) {
    }

    /**
     * Validate data in current context.
     *
     * @param DataSetInterface|mixed|RulesProviderInterface $data Data set to validate. If {@see RulesProviderInterface}
     * instance provided and rules are not specified explicitly, they are read from the
     * {@see RulesProviderInterface::getRules()}.
     * @param callable|iterable|object|string|null $rules Rules to apply. If specified, rules are not read from data set
     * even if it is an instance of {@see RulesProviderInterface}.
     *
     * @psalm-param RulesType $rules
     */
    public function validate(mixed $data, callable|iterable|object|string|null $rules = null): Result
    {
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
     * @param string $key Parameter name.
     * @param mixed $default Default value to return in case parameter with a given name does not exist.
     *
     * @return mixed Parameter value.
     *
     * @see ArrayHelper::getValue()
     */
    public function getParameter(string $key, mixed $default = null): mixed
    {
        return ArrayHelper::getValue($this->parameters, $key, $default);
    }

    public function setParameter(string $key, mixed $value): void
    {
        $this->parameters[$key] = $value;
    }

    public function isAttributeMissing(): bool
    {
        return $this->attribute !== null && $this->dataSet !== null && !$this->dataSet->hasAttribute($this->attribute);
    }
}
