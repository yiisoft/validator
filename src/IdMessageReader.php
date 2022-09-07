<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Translator\MessageReaderInterface;

final class IdMessageReader implements MessageReaderInterface
{
    public function getMessage(string $id, string $category, string $locale, array $parameters = []): ?string
    {
        return $id;
    }

    public function getMessages(string $category, string $locale): array
    {
        return [];
    }
}
