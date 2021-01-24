<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

final class ValidationContext
{
    private ?DataSetInterface $dataSet;
    private ?string $attribute = null;
    private bool $previousRulesErrored = false;
    private array $params = [];

    public function __construct(
        ?DataSetInterface $dataSet = null,
        ?string $attribute = null
    ) {
        $this->dataSet = $dataSet;
        $this->attribute = $attribute;
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

    /**
     * @return bool set to true if rule is part of a group of rules and one of the previous validations failed
     */
    public function isPreviousRulesErrored(): bool
    {
        return $this->previousRulesErrored;
    }

    public function withPreviousRulesErrored(bool $previousRulesErrored): self
    {
        $new = clone $this;
        $new->previousRulesErrored = $previousRulesErrored;
        return $new;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function withParams(array $params): self
    {
        $new = clone $this;
        $new->params = $params;
        return $new;
    }
}
