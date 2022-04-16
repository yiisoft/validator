<?php
declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Validator\Rule\RuleHandlerInterface;

interface RuleHandlerResolverInterface
{
    public function resolve(RuleInterface $rule): RuleHandlerInterface;
}
