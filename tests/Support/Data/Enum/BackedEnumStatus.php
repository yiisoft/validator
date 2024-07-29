<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data\Enum;

enum BackedEnumStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
}
