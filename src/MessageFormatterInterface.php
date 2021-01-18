<?php

declare(strict_types=1);

namespace Yiisoft\Validator;


interface MessageFormatterInterface
{
    public function format(Message $message): string;
}
