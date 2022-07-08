<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Validator\Exception\RuleHandlerInterfaceNotImplementedException;
use Yiisoft\Validator\Exception\RuleHandlerNotFoundException;
use Yiisoft\Validator\Rule\RuleHandlerInterface;

/**
 * Rule handler resolver is obtaining a resolver for a given rule class name.
 */
interface RuleHandlerResolverInterface
{
    /**
     * Obtain a resolver for a given rule class name.
     *
     * @param string $className Rule class name.
     *
     * @throws RuleHandlerNotFoundException
     * @throws RuleHandlerInterfaceNotImplementedException
     */
    public function resolve(string $className): RuleHandlerInterface;
}
