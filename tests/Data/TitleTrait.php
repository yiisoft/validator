<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Data;

use Yiisoft\Validator\Rule\HasLength\HasLength;

trait TitleTrait
{
    #[HasLength(max: 255)]
    private string $title;
}
