<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithAttributesOnly;

final class ObjectWithNestedObject
{
    #[HasLength(min: 3)]
    public string $caption = '';

    #[Nested]
    public ObjectWithAttributesOnly $object;

    public function __construct()
    {
        $this->object = new ObjectWithAttributesOnly();
    }
}
