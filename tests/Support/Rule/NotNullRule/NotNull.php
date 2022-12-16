<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Rule\NotNullRule;

use Attribute;
use Yiisoft\Validator\RuleInterface;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class NotNull implements RuleInterface
{
    public function getName(): string
    {
        return 'notNull';
    }

    public function getHandler(): string
    {
        return NotNullHandler::class;
    }
}
