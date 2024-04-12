<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Rule\StubRule;

use Yiisoft\Validator\AfterInitAttributeEventInterface;
use Yiisoft\Validator\RuleInterface;

final class StubRuleWithAfterInit implements RuleInterface, AfterInitAttributeEventInterface
{
    private object $object;

    public function getName(): string
    {
        return 'stubRuleWithAfterInit';
    }

    public function getHandler(): string
    {
        return StubRuleHandler::class;
    }

    public function afterInitAttribute(object $object): void
    {
        $this->object = $object;
    }

    public function getObject(): object
    {
        return $this->object;
    }
}
