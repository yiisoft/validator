<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

interface ValidatorFactoryInterface
{
    /**
     * @param AbstractRule[]
     */
    public function create(array $rules): ValidatorInterface;
}
