<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\Url;

final class CustomUrlRule extends Composite
{
    public function getRules(): array
    {
        return [new Required(), new Url(enableIDN: true), new HasLength(max: 20)];
    }

    public function getName(): string
    {
        return 'customUrlRule';
    }
}
