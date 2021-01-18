<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

interface ErrorMessageFormatterInterface
{
    public function format(ErrorMessage $errorMessage): string;
}
