<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data\Enum;

enum IntBackedEnumStatus: int
{
    case DRAFT = 1;
    case PUBLISHED = 2;
}
