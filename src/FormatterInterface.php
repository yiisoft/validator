<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

interface FormatterInterface
{
    /**
     * Formats the message using parameters and taking locale into account.
     *
     * @param string $message A message to format.
     * @param array $parameters Parameters to replace in the message in `['name1' => 'value1', 'name2' => 'value2']` format.
     * @psalm-param array<array-key, mixed> $parameters
     *
     * @param string $locale Locale to use when formatting. Usually affects formatting numbers, dates etc.
     *
     * @return string Formatted message.
     */
    public function format(string $message, array $parameters, string $locale): string;
}
