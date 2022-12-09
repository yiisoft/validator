<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

interface AfterInitAttributeEventInterface
{
    public function afterInitAttribute(object $object, int $target): void;
}
