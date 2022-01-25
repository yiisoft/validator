<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Attribute;

use Attribute;
use Yiisoft\Validator\Rule;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class Validate
{
    private Rule $rule;

    public function __construct(string $rule, array $parameters = [])
    {
        $this->rule = $rule::rule();

        foreach ($parameters as $methodName => $value) {
            $this->rule = $this->rule->$methodName($value);
        }
    }

    public function getRule(): Rule
    {
        return $this->rule;
    }
}
