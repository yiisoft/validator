<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Rule;

use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\Tests\Support\Rule\StubRule\StubRuleHandler;

final class RuleWithoutOptions implements RuleInterface
{
    public function getName(): string
    {
        return 'test';
    }

    public function getHandlerClassName(): string
    {
        return StubRuleHandler::class;
    }
}
