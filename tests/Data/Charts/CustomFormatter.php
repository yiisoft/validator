<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Data\Charts;

use Yiisoft\Validator\FormatterInterface;

final class CustomFormatter implements FormatterInterface
{
    public function format(string $message, array $parameters = []): string
    {
        return $message;
    }
}
