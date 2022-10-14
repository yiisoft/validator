<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Yiisoft\Validator\Rule\HasLength;

trait TitleTrait
{
    #[HasLength(max: 255)]
    private string $title;
}
