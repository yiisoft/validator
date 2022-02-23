<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Yiisoft\Validator\Rule\GroupRule;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\Url;
use Yiisoft\Validator\RuleSet;

final class CustomUrlRule extends GroupRule
{
    protected function getRuleSet(): RuleSet
    {
        return new RuleSet([new Required(), new Url(enableIDN: true), new HasLength(max: 20)]);
    }

    public function getName(): string
    {
        return 'customUrlRule';
    }
}
