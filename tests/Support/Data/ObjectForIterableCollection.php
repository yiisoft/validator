<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;

final class ObjectForIterableCollection
{
    public function __construct(
        #[Required]
        #[Length(min: 5)]
        public string $id,
        #[Required]
        #[Length(min: 5)]
        public ?string $name = null,
    ) {}
}
