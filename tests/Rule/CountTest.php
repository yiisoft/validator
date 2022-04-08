<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\Count;

class CountTest extends TestCase
{
    public function testValidateExactly(): void
    {
        $rule = new Count(exactly: 3);
        $result = $rule->validate([0, 0, 0]);

        $this->assertTrue($result->isValid(true));
    }
}
