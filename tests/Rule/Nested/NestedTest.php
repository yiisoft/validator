<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Nested;

use Yiisoft\Validator\Rule\Nested\Nested;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\Tests\Rule\AbstractRuleTest;

/**
 * @group t4
 */
final class NestedTest extends AbstractRuleTest
{
    public function optionsDataProvider(): array
    {
        return [
        ];
    }

    protected function getRule(): RuleInterface
    {
        return new Nested([]);
    }
}
