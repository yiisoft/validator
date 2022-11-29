<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Rule;

use Attribute;
use RuntimeException;
use Yiisoft\Validator\AfterInitAttributeEventInterface;
use Yiisoft\Validator\RuleInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class RuleWithCallsCount implements RuleInterface, AfterInitAttributeEventInterface
{
    public static int $afterInitAttributeCallsCount = 0;

    public function afterInitAttribute(object $object): void
    {
        self::$afterInitAttributeCallsCount++;
    }

    public function getName(): string
    {
        return 'rule-with-calls-count';
    }

    public function getHandlerClassName(): string
    {
        throw new RuntimeException();
    }
}
