<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Attribute;
use RuntimeException;
use Yiisoft\Validator\AfterInitAttributeEventInterface;
use Yiisoft\Validator\RuleInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class RuleWithCalls implements RuleInterface, AfterInitAttributeEventInterface
{
    public static int $countCalls = 0;

    public function afterInitAttribute(object $object): void
    {
        self::$countCalls++;
    }

    public function getName(): string
    {
        return 'rule-with-calls';
    }

    public function getHandlerClassName(): string
    {
        throw new RuntimeException();
    }
}
