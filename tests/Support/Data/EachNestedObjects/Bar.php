<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data\EachNestedObjects;

use Yiisoft\Validator\Rule\Required;

final class Bar
{
    #[Required]
    public ?string $name = null;
}
