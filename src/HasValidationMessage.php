<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

trait HasValidationMessage
{
    public function message(string $message): self
    {
        $new = clone $this;
        $new->message = $message;
        return $new;
    }
}
