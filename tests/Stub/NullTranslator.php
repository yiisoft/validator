<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Exception;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\TranslatorInterface;

class NullTranslator implements TranslatorInterface
{
    public function addCategorySource(CategorySource $category): void
    {
    }

    public function addCategorySources(array $categories): void
    {
    }

    public function setLocale(string $locale): void
    {
    }

    public function getLocale(): string
    {
        throw new Exception('Not implemented yet');
    }

    public function translate(
        string $id,
        array $parameters = [],
        string $category = null,
        string $locale = null
    ): string {
        return $id;
    }

    public function withCategory(string $category): TranslatorInterface
    {
        throw new Exception('Not implemented yet');
    }

    public function withLocale(string $locale): TranslatorInterface
    {
        throw new Exception('Not implemented yet');
    }
}
