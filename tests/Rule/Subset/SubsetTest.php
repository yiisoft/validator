<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Subset;

use Yiisoft\Validator\Rule\Subset\Subset;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\Tests\Rule\AbstractRuleTest;

/**
 * @group t
 */
final class SubsetTest extends AbstractRuleTest
{
    public function optionsDataProvider(): array
    {
        return [
            [
                new Subset([]),
                [
                    'values' => [],
                    'strict' => false,
                    'iterableMessage' => [
                        'message' => 'Value must be iterable.',
                    ],
                    'subsetMessage' => [
                        'message' => 'Values must be ones of {values}.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ]
            ]
        ];
    }

    protected function getRule(): RuleInterface
    {
        return new Subset([]);
    }
}
