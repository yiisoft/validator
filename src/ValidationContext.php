<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Arrays\ArrayHelper;

/**
 * Validation context that rule may take into account when performing validation.
 */
final class ValidationContext
{
    /**
     * @param DataSetInterface|null $dataSet Data set the attribute belongs to. Null if a single value is validated.
     * @param string|null $attribute Validated attribute name. Null if a single value is validated.
     * @param array $parameters Arbitrary parameters.
     */
    public function __construct(
        private ValidatorInterface $validator,
        private ?DataSetInterface $dataSet,
        private ?string $attribute = null,
        private array $parameters = []
    ) {
    }

    public function getValidator(): ValidatorInterface
    {
        return $this->validator;
    }

    /**
     * @return DataSetInterface|null Data set the attribute belongs to. Null if a single value is validated.
     */
    public function getDataSet(): ?DataSetInterface
    {
        return $this->dataSet;
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
    public function withAttribute(?string $attribute): self
    {
        $new = clone $this;
        $new->attribute = $attribute;
        return $new;
    }

    /**
     * @return array Arbitrary parameters.
     */
    public function getParameters(): array
    {
        return $this->parameters;
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
