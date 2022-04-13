<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Subset;

use Yiisoft\Validator\ParametrizedRuleInterface;
use Yiisoft\Validator\Rule\Subset\Subset;
use Yiisoft\Validator\Tests\Rule\AbstractRuleTest;

/**
 * @group t2
 */
final class SubsetTest extends AbstractRuleTest
{
    public function optionsDataProvider(): array
    {
        return [
        ];
    }

    protected function getRule(): ParametrizedRuleInterface
    {
        return new Subset([]);
    }
}
