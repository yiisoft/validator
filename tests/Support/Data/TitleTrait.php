<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Yiisoft\Validator\Rule\Length;

trait TitleTrait
{
    #[Length(max: 255)]
    private string $title;
}
