<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use MessageFormatter;

use function is_array;
use function is_object;
use function is_resource;

final class Formatter implements FormatterInterface
{
    private ?string $locale = null;

    /**
     * This method uses \MessageFormatter::format()
     *
     * @link https://php.net/manual/en/messageformatter.format.php
     */
    public function format(string $message, array $parameters, string $locale = 'en-US'): string
    {
        if ($parameters === []) {
            return $message;
        }

        $replacements = [];

        foreach ($parameters as $key => $value) {
            if (is_array($value)) {
                $value = 'array';
            } elseif (is_object($value)) {
                $value = 'object';
            } elseif (is_resource($value)) {
                $value = 'resource';
            }
            $replacements[$key] = $value;
        }

        $locale = $this->locale ?? $locale;
        $formatter = new MessageFormatter($locale, $message);
        $result = $formatter->format($replacements);

        if ($result === false) {
            return $message;
        }

        return $result;
    }

    /**
     * Set the locale to use for formatting.
     *
     * @param string $locale The locale to use for formatting.
     *
     * @return self
     */
    public function locale(string $locale): self
    {
        $new = clone $this;
        $new->locale = $locale;
        return $new;
    }
}
