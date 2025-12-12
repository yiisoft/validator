<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Base;

use Closure;
use Yiisoft\Validator\WhenInterface;

trait WhenTestTrait
{
    abstract public function testWhen(): void;

    private function testWhenInternal(WhenInterface $ruleWithoutWhen, WhenInterface $ruleWithWhen): void
    {
        $this->assertNull($ruleWithoutWhen->getWhen());

        $when = static fn(mixed $value): bool => $value !== null;
        $new = $ruleWithoutWhen->when($when);
        $this->assertNull($ruleWithoutWhen->getWhen());
        $this->assertInstanceOf(Closure::class, $new->getWhen());
        $this->assertNotSame($ruleWithoutWhen, $new);

        $this->assertInstanceOf(Closure::class, $ruleWithWhen->getWhen());

        $new = $ruleWithWhen->when(null);
        $this->assertInstanceOf(Closure::class, $ruleWithWhen->getWhen());
        $this->assertNull($new->getWhen());
        $this->assertNotSame($ruleWithWhen, $new);
    }
}
