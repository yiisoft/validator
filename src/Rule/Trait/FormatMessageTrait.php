<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Trait;

use Yiisoft\Validator\Formatter;

trait FormatMessageTrait
{
    private ?Formatter $formatter = null;

    public function formatMessage(string $message, array $parameters = []): string
    {
        if ($this->formatter === null) {
            $this->formatter = new Formatter();
        }

        return $this->formatter->format($message, $parameters);
    }
}
