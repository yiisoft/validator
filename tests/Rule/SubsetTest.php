<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Rule\Subset;
use Yiisoft\Validator\ParametrizedRuleInterface;

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
                ],
            ],
        ];
    }

    protected function getRule(): ParametrizedRuleInterface
    {
        return new Subset([]);
    }
}
