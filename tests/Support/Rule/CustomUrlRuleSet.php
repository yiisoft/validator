<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Rule;

use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\Url;

final class CustomUrlRuleSet extends Composite
{
    public function getRules(): iterable
    {
        yield new Required();
        yield new Url(enableIdn: true);
        yield new Length(max: 20);
    }

    public function getName(): string
    {
        return 'customUrlRule';
    }
}
