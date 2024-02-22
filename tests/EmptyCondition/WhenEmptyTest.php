<?php

declare(strict_types=1);

namespace EmptyCondition;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\EmptyCondition\WhenEmpty;

final class WhenEmptyTest extends TestCase
{
    public function testDefault(): void
    {
        $condition = new WhenEmpty();

        $result = $condition('test');

        $this->assertFalse($result);
    }
}
