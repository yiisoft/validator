<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Yiisoft\Validator\Rule\Length;

final class ObjectWithAttributesOnly
{
    #[Length(min: 5)]
    public string $name = '';
}
