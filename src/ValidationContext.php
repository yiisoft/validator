<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Arrays\ArrayHelper;

final class ValidationContext
{
    private ?DataSetInterface $dataSet;
    private ?string $attribute = null;
    private array $params = [];

    public function __construct(
        ?DataSetInterface $dataSet = null,
        ?string $attribute = null,
        array $params = []
    ) {
        $this->dataSet = $dataSet;
        $this->attribute = $attribute;
        $this->params = $params;
    }

    /**
     * @return DataSetInterface|null Optional data set.
     */
    public function getDataSet(): ?DataSetInterface
    {
        return $this->dataSet;
    }

    public function getAttribute(): ?string
    {
        return $this->attribute;
    }

    public function withAttribute(?string $attribute): self
    {
        $new = clone $this;
        $new->attribute = $attribute;
        return $new;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getParam(string $key, $default = null)
    {
        return ArrayHelper::getValue($this->params, $key, $default);
    }

    public function setParam(string $key, $value): void
    {
        $this->params[$key] = $value;
    }
}
