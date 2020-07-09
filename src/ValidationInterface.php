<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

interface ValidationInterface
{
    /**
     * @param Rule[]
     */
    public function create(array $rules): ValidatorInterface;
}
