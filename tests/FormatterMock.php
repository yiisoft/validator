<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\ErrorMessage;
use Yiisoft\Validator\ErrorMessageFormatterInterface;

abstract class FormatterMock extends TestCase
{
    protected function createFormatterMock(): ErrorMessageFormatterInterface
    {
        return new class() implements ErrorMessageFormatterInterface {
            public function format(ErrorMessage $errorMessage): string
            {
                $replacements = [];
                foreach ($errorMessage->getParameters() as $key => $value) {
                    if ($value instanceof ErrorMessage) {
                        $value = $this->format($value);
                    } elseif (is_array($value)) {
                        $value = 'array';
                    } elseif (is_object($value)) {
                        $value = 'object';
                    } elseif (is_resource($value)) {
                        $value = 'resource';
                    }
                    $replacements['{' . $key . '}'] = $value;
                }
                return 'Translate: '.strtr($errorMessage->getMessage(), $replacements);
            }
        };
    }

}
