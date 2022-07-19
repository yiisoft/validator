<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Yiisoft\Validator\Rule\GroupRule;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\Url;

final class CustomUrlRule extends GroupRule
{
    public function getRuleSet(): iterable
    {
        yield new Required();
        yield new Url(enableIDN: true);
        yield new HasLength(max: 20);
    }

    public function getName(): string
    {
        return 'customUrlRule';
    }
}
