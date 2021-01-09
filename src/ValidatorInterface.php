<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

interface ValidatorInterface
{
    /**
     * @param DataSetInterface $dataSet
     * @param Rule[] $rules
     *
     * @return ResultSet
     */
    public function validate(DataSetInterface $dataSet, iterable $rules): ResultSet;
}
