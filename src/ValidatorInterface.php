<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

interface ValidatorInterface
{
    public function validate(DataSetInterface $dataSet): ResultSet;

    /**
     * @param string $attribute
     * @param callable|Rule $rule
     */
    public function addRule(string $attribute, $rule): void;

    public function asArray(?ErrorMessageFormatterInterface $formatter = null): array;
}
