<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Yiisoft\Validator\Rule\GroupRule;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\Url;
use Yiisoft\Validator\Rules;

final class CustomUrlRule extends GroupRule
{
    public static function rule(): self
    {
        return new self();
    }

    protected function getRules(): Rules
    {
        return new Rules(
            [
                new Required(),
                Url::rule()->enableIDN(),
                (new HasLength())->max(20),
            ]
        );
    }

    public function getName(): string
    {
        return 'customUrlRule';
    }
}
