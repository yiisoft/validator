<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Base;

use Yiisoft\Validator\SkipOnErrorInterface;

trait SkipOnErrorTestTrait
{
    abstract public function testSkipOnError(): void;

    private function testSkipOnErrorInternal(
        SkipOnErrorInterface $nonSkippingRule,
        SkipOnErrorInterface $skippingRule,
    ): void {
        $this->assertFalse($nonSkippingRule->shouldSkipOnError());

        $new = $nonSkippingRule->skipOnError(true);
        $this->assertFalse($nonSkippingRule->shouldSkipOnError());
        $this->assertTrue($new->shouldSkipOnError());
        $this->assertNotSame($nonSkippingRule, $new);

        $this->assertTrue($skippingRule->shouldSkipOnError());

        $new = $skippingRule->skipOnError(false);
        $this->assertTrue($skippingRule->shouldSkipOnError());
        $this->assertFalse($new->shouldSkipOnError());
        $this->assertNotSame($skippingRule, $new);
    }
}
