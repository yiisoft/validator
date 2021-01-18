<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

final class ErrorMessageFormatter implements ErrorMessageFormatterInterface
{
    public function format(ErrorMessage $errorMessage): string
    {
        $replacements = [];
        foreach ($errorMessage->getParameters() as $key => $value) {
            if (is_array($value)) {
                $value = 'array';
            } elseif (is_object($value)) {
                $value = 'object';
            } elseif (is_resource($value)) {
                $value = 'resource';
            }
            $replacements['{' . $key . '}'] = $value;
        }
        return strtr($errorMessage->getMessage(), $replacements);
    }
}
