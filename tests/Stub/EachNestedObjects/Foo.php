<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub\EachNestedObjects;

use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Required;

final class Foo
{
    #[Required]
    public ?string $name = null;

    #[Each([
        new Nested(Bar::class),
    ])]
    public array $bars;

    public function __construct()
    {
        $this->bars = [
            new Bar(),
        ];
    }
}
