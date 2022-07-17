<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\TranslatorInterface;

use function is_array;
use function is_object;
use function is_resource;

final class FallbackTranslator implements TranslatorInterface
{
    public function addCategorySource(CategorySource $category): void
    {
        // do nothing
    }

    public function addCategorySources(array $categories): void
    {
        // do nothing
    }

    public function setLocale(string $locale): void
    {
        // do nothing
    }

    public function getLocale(): string
    {
        return 'en';
    }

    public function translate(
        string $id,
        array $parameters = [],
        string $category = null,
        string $locale = null
    ): string {
        $replacements = [];
        foreach ($parameters as $key => $value) {
            if (is_array($value)) {
                $value = 'array';
            } elseif (is_object($value)) {
                $value = 'object';
            } elseif (is_resource($value)) {
                $value = 'resource';
            }
            $replacements['{' . $key . '}'] = $value;
        }
        return strtr($id, $replacements);
    }

    public function withCategory(string $category): TranslatorInterface
    {
        return $this;
    }

    public function withLocale(string $locale): TranslatorInterface
    {
        return $this;
    }
}
