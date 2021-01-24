<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

interface ValidatorInterface
{
    /**
     * @param DataSetInterface $dataSet
     * @param Rule[] $rules
     * @psalm-param iterable<string, Rule[]> $rules
     *
     * @return ResultSet
     */
    public function validate(DataSetInterface $dataSet, iterable $rules): ResultSet;
}
