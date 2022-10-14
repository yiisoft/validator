<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Yiisoft\Validator\Rule\HasLength;

final class ObjectWithAttributesOnly
{
    #[HasLength(min: 5)]
    public string $name = '';
}
