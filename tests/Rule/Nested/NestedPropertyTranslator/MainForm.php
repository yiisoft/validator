<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Nested\NestedPropertyTranslator;

use Yiisoft\Validator\Rule\Nested;

final class MainForm
{
    public function __construct(
        #[Nested]
        public SubForm $sub,
    ) {}
}
