<?php
declare(strict_types=1);

namespace Yiisoft\Validator;

interface FormatterInterface
{
    public function format(string $message, array $parameters = []): string;
}
