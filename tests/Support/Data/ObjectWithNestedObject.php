<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Nested;

final class ObjectWithNestedObject
{
    #[Length(min: 3)]
    public string $caption = '';

    #[Nested]
    public ObjectWithAttributesOnly $object;

    public function __construct()
    {
        $this->object = new ObjectWithAttributesOnly();
    }
}
